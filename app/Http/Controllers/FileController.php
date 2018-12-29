<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App;
use Auth;
use App\Subject;
use App\Branch;
use App\Level;
use Illuminate\Support\Facades\Crypt;
use App\Category;
use App\File;
use \Validator;
use Cookie;
use Voyager;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use TCG\Voyager\Http\Controllers\Controller;


class FileController extends Controller
{
    use BreadRelationshipParser;

    private $storage;

    public function __construct()
    {
        $this->middleware('auth', ['except' => []]);
        $this->storage = public_path('/storage/files');
    }


    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object)['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
        $searchable = $dataType->server_side ? array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray()) : '';
        $orderBy = $request->get('order_by');
        $sortOrder = $request->get('sort_order', null);

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $relationships = $this->getRelationships($dataType);

            $model = app($dataType->model_name);
            if (Auth::user()->role_id != 1) {
                // If it's a, only show their own files   
                $query = $model::select('*')->with($relationships)->where('user_id', '=', Auth::user()->id);
            } else {
                $query = $model::select('*')->with($relationships);
            }
            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%' . $search->value . '%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'DESC';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);
            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        if (($isModelTranslatable = is_bread_translatable($model))) {
            $dataTypeContent->load('translations');
        }

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        $view = 'files.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'sortOrder',
            'searchable',
            'isServerSide'
        ));
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |__) |
    //               |  _  /
    //               | | \ \
    //               |_|  \_\
    //
    //  Read an item of our Data Type B(R)EAD
    //
    //****************************************

    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $relationships = $this->getRelationships($dataType);
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $dataTypeContent = call_user_func([$model->with($relationships), 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name

            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $relationships = $this->getRelationships($dataType);
        if (Auth::user()->role_id != 1) {
                // If it's a teacher, only show their own files   
            $dataTypeContent = (strlen($dataType->model_name) != 0)
                ? app($dataType->model_name)->with($relationships)->where('user_id', '=', Auth::user()->id)->findOrFail($id)
                : DB::table($dataType->name)->where('id', $id)->where('user_id', '=', Auth::user()->id)->first(); // If Model doest exist, get data from table name;
        } else {
            $dataTypeContent = (strlen($dataType->model_name) != 0)
                ? app($dataType->model_name)->with($relationships)->findOrFail($id)
                : DB::table($dataType->name)->where('id', $id)->first(); // If Model doest exist, get data from table name
        }
        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;

        $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->ajax()) {
            // TODO Clean this up! 
            $file = File::findOrfail($id);
            $oldID = $file->user_id;
            $newID = $request->user_id;
            $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
            // If teacher tries to assign another user to his own shit !
            if ($oldID != $newID) {
                $file->user_id = $oldID;
                $file->save();
            }
            event(new BreadDataUpdated($dataType, $data));

            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message' => __('voyager::generic.successfully_updated') . " {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
    }

    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->has('_validate')) {
            $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

            event(new BreadDataAdded($dataType, $data));

            if ($request->ajax()) {
                return response()->json(['success' => true, 'data' => $data]);
            }

            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message' => __('voyager::generic.successfully_added_new') . " {$dataType->display_name_singular}",
                    'alert-type' => 'success',
                ]);
        }
    }

    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item BREA(D)
    //
    //****************************************

    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('delete', app($dataType->model_name));

        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
            $this->cleanup($dataType, $data);
        }

        $displayName = count($ids) > 1 ? $dataType->display_name_plural : $dataType->display_name_singular;

        $res = $data->destroy($ids);
        $data = $res
            ? [
            'message' => __('voyager::generic.successfully_deleted') . " {$displayName}",
            'alert-type' => 'success',
        ]
            : [
            'message' => __('voyager::generic.error_deleting') . " {$displayName}",
            'alert-type' => 'error',
        ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    /**
     * Remove translations, images and files related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $dataType
     * @param \Illuminate\Database\Eloquent\Model $data
     *
     * @return void
     */
    protected function cleanup($dataType, $data)
    {
        // Delete Translations, if present
        if (is_bread_translatable($data)) {
            $data->deleteAttributeTranslations($data->getTranslatableAttributes());
        }

        // Delete Images
        $this->deleteBreadImages($data, $dataType->deleteRows->where('type', 'image'));

        // Delete Files
        foreach ($dataType->deleteRows->where('type', 'file') as $row) {
            if (isset($data->{$row->field})) {
                foreach (json_decode($data->{$row->field}) as $file) {
                    $this->deleteFileIfExists($file->download_link);
                }
            }
        }
    }

    /**
     * Delete all images related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param \Illuminate\Database\Eloquent\Model $rows
     *
     * @return void
     */
    public function deleteBreadImages($data, $rows)
    {
        foreach ($rows as $row) {
            if ($data->{$row->field} != config('voyager.user.default_avatar')) {
                $this->deleteFileIfExists($data->{$row->field});
            }

            if (isset($row->details->thumbnails)) {
                foreach ($row->details->thumbnails as $thumbnail) {
                    $ext = explode('.', $data->{$row->field});
                    $extension = '.' . $ext[count($ext) - 1];

                    $path = str_replace($extension, '', $data->{$row->field});

                    $thumb_name = $thumbnail->name;

                    $this->deleteFileIfExists($path . '-' . $thumb_name . $extension);
                }
            }
        }

        if ($rows->count() > 0) {
            event(new BreadImagesDeleted($data, $rows));
        }
    }

    /**
     * Order BREAD items.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        if (!isset($dataType->order_column) || !isset($dataType->order_display_column)) {
            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message' => __('voyager::bread.ordering_not_set'),
                    'alert-type' => 'error',
                ]);
        }

        $model = app($dataType->model_name);
        $results = $model->orderBy($dataType->order_column)->get();

        $display_column = $dataType->order_display_column;

        $view = 'voyager::bread.order';

        if (view()->exists("voyager::$slug.order")) {
            $view = "voyager::$slug.order";
        }

        return Voyager::view($view, compact(
            'dataType',
            'display_column',
            'results'
        ));
    }

    public function update_order(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('edit', app($dataType->model_name));

        $model = app($dataType->model_name);

        $order = json_decode($request->input('order'));
        $column = $dataType->order_column;
        foreach ($order as $key => $item) {
            $i = $model->findOrFail($item->id);
            $i->$column = ($key + 1);
            $i->save();
        }
    }
    public function showFile($id)
    {

        $file = File::find($id);
        if (!$file) {
            return redirect('/home');
        }

        $data = [
            "file" => $file,
            "showSearch" => false,
            "showFilter" => false,
        ];

        return view('single-file', $data);
    }
    public function addFile()
    {
        if (Auth::user()->role_id == 4) {
            return redirect('/home');
            die();
        }
        $levelsAll = Level::all();
        foreach ($levelsAll as $level) {
            $levels[$level->parent][] = $level;
        }
        $branchesAll = Branch::all();
        foreach ($branchesAll as $branch) {
            $branches[$branch->niveau][] = $branch;
        }
        $data = [
            "subjects" => Subject::all(),
            "levels" => $levels,
            "branches" => $branches,
            "categories" => Category::all()
        ];

        return view('add-file', $data);
    }
    public function handleAddFile(Request $request)
    {
        $validator = \Validator::make(\Request::all(), [
            'file' => 'mimes:pdf',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Fichier non supporté.'], 403);
        }

        $pdf = $request->file('file');

        if (!is_dir($this->storage)) {
            mkdir($this->storage, 0777);
        }

        $name = sha1(date('YmdHis') . str_random(30));
        $save_name = $name . '.' . $pdf->getClientOriginalExtension();
        $pdf->move($this->storage, $save_name);

        return response()->json([
            'message' => 'File saved Successfully',
            'filename' => $pdf->getClientOriginalName(),
            'hash' => Crypt::encryptString($save_name)
        ], 200);
    }

    public function storeFile(Request $request)
    {

        $rules = [
            'title' => 'required|string|max:255',
            'subject' => 'required',
            'level' => 'required',
            'branch' => 'required',
            'hash' => 'required',
            'description' => 'required',
            'keywords' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $client = File::create([
            'title' => $request->input('title'),
            'filename' => Crypt::decryptString($request->input('hash')),
            'subject_id' => $request->input('subject') == 0 ? '' : $request->input('subject'),
            'level_id' => $request->input('level') == 0 ? '' : $request->input('level'),
            'branch_id' => $request->input('branch') == 0 ? '' : $request->input('branch'),
            'category_id' => $request->input('category') == 0 ? '' : $request->input('category'),
            'description' => $request->input('description'),
            'keywords' => $request->input('keywords'),
            'user_id' => Auth::user()->id
        ]);
        Auth::user()->rewardFor('File Upload', config('rewards.files.posted_file'));
        return redirect('/upload')->with('success', "Fichier Ajouté !");
    }
    public function download($uuid)
    {
        $id = Crypt::decryptString($uuid);
        $file = File::where('id', $id)->firstOrFail();
        $file->downloads = $file->downloads + 1;
        $file->save();
        $pathToFile = public_path('/storage/files/' . $file->filename);
        return response()->download($pathToFile, str_slug($file->title) . '.pdf');
    }
    public function view($uuid)
    {
        $id = Crypt::decryptString($uuid);
        $file = File::where('id', $id)->firstOrFail();
        $file->views = $file->views + 1;
        $file->save();
        $pathToFile = public_path('/storage/files/' . $file->filename);
        return response()->file($pathToFile);
    }
    public function upvote(Request $request)
    {
        $rules = [
            'file_id' => 'exists:files,id'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => 'Something went wrong, but we saved your vote!']);
        }
        $id = $request->input('file_id');
        $file = \App\File::find($id);
        // TODO: Log the user actions & use that to verify votes.
        return $this->updateVote($request, true, $id);
    }
    public function downvote(Request $request)
    {
        $rules = [
            'file_id' => 'exists:files,id'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => 'Something went wrong, but we saved your vote!']);
        }
        $id = $request->input('file_id'); 
        // TODO: Log the user actions & use that to verify votes.
        return $this->updateVote($request, false, $id);
    }
    /**
     * updateVote - Manages the cookies for the voting system, 
     *
     * @param bool $voteType true:upVote!
     * @param int $id
     * @return void
     */
    public function updateVote($request, $voteType, $id)
    {
        $otherVoteType = !$voteType ? 'up_voted' : 'down_voted';
        $voteType = $voteType ? 'up_voted' : 'down_voted';
        $decrement = false;
        $file = \App\File::find($id);
        if (Auth::user()->id == $file->user_id && Auth::user()->id != 1) {
            // Fix: Only allow the super admin to like his own shit !
            return response()->json(['errors' => 'You can\'t rate your own file silly!']);
        }
        if ($request->hasCookie($voteType) && $request->hasCookie($otherVoteType)) {
            // IF both cookie types exist !
            $votedFiles = json_decode(Crypt::decryptString(Cookie::get($voteType)), true);
            $otherVotedFiles = json_decode(Crypt::decryptString(Cookie::get($otherVoteType)), true);
            if (in_array($id, $votedFiles['files'])) {
                // If I already votedType this file return error
                $verb = implode('', explode('_', $voteType));
                return response()->json(['errors' => 'You have already ' . $verb . ' this file!']);
            } else {
                // Else, check if it's otherVotedType
                if (in_array($id, $otherVotedFiles['files'])) {
                    //If it is, delete it
                    $otherVotedFiles['files'] = array_diff($otherVotedFiles['files'], [$id]);
                    $decrement = true;
                    // and add it to the proper votedType
                    $votedFiles['files'][] = $id;
                } else {
                    // if it's not in otherVotedType, just add it to the proper one.
                    $votedFiles['files'][] = $id;
                }

                /* Reconstruct both cookies */
                unset($_COOKIE[$voteType]);
                unset($_COOKIE[$otherVoteType]);
                Cookie::queue($otherVoteType, Crypt::encryptString(json_encode($otherVotedFiles)), 2628000);
                Cookie::queue($voteType, Crypt::encryptString(json_encode($votedFiles)), 2628000);
                $column = $voteType == 'up_voted' ? 'upvotes' : 'downvotes';
                $otherColumn = $column == 'upvotes' ? 'downvotes' : 'upvotes';
                $file->increment($column);
                if ($decrement) {
                    $file->decrement($otherColumn);
                } else {
                    // Only reward when it's first time
                    // Reward user & file owner
                    $amount = ($voteType == 'up_voted') ? config('rewards.files.voting.upvoted') : config('rewards.files.voting.downvoted');
                    Auth::user()->rewardFor('Vote', $amount, config('rewards.files.voting.owner'), 'App\File', $file->id);
                }
                $file->save();
            }
        }
        return response()->json(['success' => 'Vote registered !']);
    }
}

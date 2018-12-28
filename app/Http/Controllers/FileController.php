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
use TCG\Voyager\Voyager;
use Cookie;

class FileController extends Controller
{
    private $storage;

    public function __construct()
    {
        $this->middleware('auth', ['except' => []]);
        $this->storage = public_path('/storage/files');
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
            'user_id' => Auth::user()->id
        ]);

        return redirect(locale()->current() . '/upload')->with('success', "Fichier Ajouté !");
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
                }
                $file->save();

                // Reward user & file owner
                $amount = ($voteType == 'up_voted') ? config('rewards.files.voting.upvoted') : config('rewards.files.voting.downvoted');
                Auth::user()->rewardFor('Vote', $amount, config('rewards.files.voting.owner'), 'App\File', $file->id);
            }
        }
        return response()->json(['success' => 'Vote registered !']);
    }
}

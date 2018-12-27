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
}

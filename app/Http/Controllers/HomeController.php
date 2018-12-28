<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use App\Subject;
use App\Branch;
use App\File;
use Cookie;
use Illuminate\Support\Facades\Crypt;
use App\Level;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified'], ['except' => ['welcome']]);
        if (Cookie::get('up_voted') === null) {
            Cookie::queue('up_voted', Crypt::encryptString(json_encode(['files' => []])), 2628000);
        }
        if (Cookie::get('down_voted') === null) {
            Cookie::queue('down_voted', Crypt::encryptString(json_encode(['files' => []])), 2628000);
        }
    }

    public function welcome()
    {
        if (Auth::check()) {
            if (Auth::user()->verified()) {
                return redirect('/home');
            }
        }
        return view('welcome');
    }

    /**
     * Gera a paginação dos itens de um array ou collection.
     *
     * @param array|Collection      $items
     * @param int   $perPage
     * @param int  $page
     * @param array $options
     *
     * @return LengthAwarePaginator
     */
    public function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ? : (Paginator::resolveCurrentPage() ? : 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sort = null)
    {

        $levelsAll = Level::all();
        foreach ($levelsAll as $level) {
            $levels[$level->parent][] = $level;
        }
        $branchesAll = Branch::all();
        foreach ($branchesAll as $branch) {
            $branches[$branch->niveau][] = $branch;
        }

        switch ($sort) {
            case "recent":
                $files = File::all()->sortByDesc("id");
                break;
            case "popular":
                $files = File::all()->sortByDesc("views");
                break;
            default:
                $files = File::all()->sortByDesc("id");
                break;
        }

        $files = $this->paginate($files, setting('site.files_per_page'));
        $prefix = $sort ? '/home/' . $sort : '/home';
        $files->withPath($prefix);

        $data = [
            "subjects" => Subject::all(),
            "levels" => $levels,
            "branches" => $branches,
            "files" => $files,
        ];
        return view('home', $data);
    }
    public function search(Request $request)
    {
        $sort = null;
        $levelsAll = Level::all();
        foreach ($levelsAll as $level) {
            $levels[$level->parent][] = $level;
        }
        $branchesAll = Branch::all();
        foreach ($branchesAll as $branch) {
            $branches[$branch->niveau][] = $branch;
        }

        $term = $request->input('q');
        $files = \App\File::whereHas('category', function ($query) use ($term) {
            $query->where('title', 'like', '%' . $term . '%');
        })
            ->orwhereHas('subject', function ($query) use ($term) {
                $query->where('title', 'like', '%' . $term . '%');
            })
            ->orwhereHas('branch', function ($query) use ($term) {
                $query->where('title', 'like', '%' . $term . '%');
            })
            ->orwhereHas('level', function ($query) use ($term) {
                $query->where('title', 'like', '%' . $term . '%');
            })
            ->orwhereHas('level', function ($query) use ($term) {
                $query->where('parent', 'like', '%' . $term . '%');
            })
            ->orwhereHas('user', function ($query) use ($term) {
                $query->where('first_name', 'like', '%' . $term . '%')->orWhere('last_name', 'like', '%' . $term . '%');
            })
            ->orWhere('title', 'LIKE', '%' . $term . '%')
            ->get();
        if (count($files) > 0) {

            $files = $this->paginate($files, setting('site.files_per_page'));
            $prefix = $sort ? '/home/' . $sort : '/home';
            $files->withPath($prefix);
            $data = [
                "subjects" => Subject::all(),
                "levels" => $levels,
                "branches" => $branches,
                "files" => $files,
                "searchTerm" => $term
            ];
            return view('home', $data);
        } else {
            return redirect('/home')->with('message', 'No Details found. Try to search again !');

        }
    }
    public function searchRedirect()
    {
        return redirect('/home');
    }
    public function verified()
    {
        $referrer = \App\User::where('affiliate_id', '=', Auth::user()->referred_by)->first();
        $referred = \App\User::find(Auth::user()->id);
        if (!is_null($referrer)) {
            $referred->rewardFor('Refferal Sign Up', config('rewards.referral.verified_account.referred'), config('rewards.referral.verified_account.referrer'), null, null, $referrer->id);
        }
        return redirect('/home');
    }
}
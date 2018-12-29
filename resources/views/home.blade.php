@extends('layouts.frontend')
@section('title','Archive Exam')
@section('add2header')
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt"
    crossorigin="anonymous">
<link rel="stylesheet" href="https://carlosroso.com/notyf/notyf.min.css" />
@endsection

@section('content')

<div class="container">
    @guest
    @else
    <div class="row" style="height:2rem;"></div>

    @if (session('message'))
    <div class="alert alert-primary alert-dismissable">
        <h4>
            <i aria-hidden="true" class="icon fa fa-info-circle fa-fw"></i>
        </h4>
        <a aria-label="close" class="close" data-dismiss="alert" href="#">&times;</a>
        {{ session('message') }}
    </div>
    @endif
    <div class="row">
        <div class="col-9" id="barba-wrapper">
            <div class="row">
                <div class="col-5">
                    <a href="/home/popular" class="btn btn-outline-dark" style="border-radius:0;"><i class="fa fa-fire"></i>
                        Populaire</a>
                    <a href="/home/recent" class="btn btn-outline-dark" style="border-radius:0;"><i class="fa fa-sort-amount-down"></i>
                        Plus Recent</a>
                </div>
                <div class="col-4">
                    @if($files)
                    {{ $files->links() }}
                    @endif </div>
                <div class="col-3">
                    @if(Voyager::can('add_files'))
                    <a href="/upload" class="btn btn-outline-primary" style="border-radius:0;float:right;"><i class="fa fa-file-upload"></i>
                        Ajouter un fichier</a>
                    @endif
                </div>
            </div>

            <hr>
            @if(isset($searchTerm)) <h4>Résultats de recherche : <b>{{ $searchTerm }}</b></h4> <br>@endif
            @if($files->count() > 0)
            <div id="noResultsContainer" style="display:none;">
                <h4>Pas de résultats répondant à vos critères.</h4>
            </div>
            <div class="row file-list hideOverflow barba-container grid">
                @foreach($files as $file)
                <div class="col-12 grid-item {{ 'subject_'.$file->subject_id }} {{ 'level_'.$file->level_id }} {{ 'branch_'.$file->branch_id }}">
                    <div class="card">
                        <div class="card-body row">
                            <div class="col-1">
                                <div class="rating">
                                    @php
                                    $userVote = Auth::user()->fileVote($file->id);
                                    @endphp
                                    <button id="upButton" type="button" onclick="upVote({{ $file->id }})" class="vote">

                                        <svg class="upArrow" viewBox="0 0 11.5 6.4" xml:space="preserve">
                                            <path @if($userVote==1) style="fill: #6CC576" @endif data_id="{{ $file->id }}"
                                                d="M11.4,5.4L6,0C5.9-0.1,5.8-0.1,5.8-0.1c-0.1,0-0.2,0-0.2,0.1
	L0.1,5.4C0,5.6,0,5.7,0.1,5.9l0.4,0.4c0.1,0.1,0.3,0.1,0.4,0l4.8-4.8l4.8,4.8c0.1,0.1,0.3,0.1,0.4,0l0.4-0.4
	C11.5,5.7,11.5,5.6,11.4,5.4z" />
                                        </svg>
                                    </button>
                                    <h3 id="scoreCounter" data_id="{{ $file->id }}">{{ $file->upvotes-$file->downvotes
                                        }}</h3>
                                    <button id="downButton" type="button" onclick="downVote({{ $file->id }})" class="vote">

                                        <svg class="downArrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                            viewBox="0 0 11.5 6.4" xml:space="preserve">
                                            <path @if($userVote==-1) style="fill: #FF586C" @endif data_id="{{ $file->id }}"
                                                d="M0.1,0.9l5.4,5.4c0.1,0.1,0.1,0.1,0.2,0.1c0.1,0,0.2,0,0.2-0.1
	l5.4-5.4c0.1-0.1,0.1-0.3,0-0.4L11,0c-0.1-0.1-0.3-0.1-0.4,0L5.8,4.8L0.9,0C0.8-0.1,0.6-0.1,0.5,0L0.1,0.4C0,0.6,0,0.7,0.1,0.9z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="col-11">
                                <div class="row">
                                    <div class="col-10">
                                        <h5 class="card-title hideOverflow">{{ $file->title }}</h5>
                                    </div>
                                    <div class="col-2">
                                        <div class="row">
                                            <div class="col-6">{{ $file->views }} <i class="fa fa-eye"></i></div>
                                            <div class="col-6">{{ $file->downloads }} <i class="fa fa-download"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="card-text">Proposé par: <b>
                                        @if($file->user->first_name && $file->user->last_name)
                                        {{ substr($file->user->first_name,0,1).'. '.strtoupper($file->user->last_name)
                                        }}
                                        @else
                                        {{ $file->user->name }}
                                        @endif
                                    </b></p>
                                <div class="pull-right">
                                    <a href="/{{ \Illuminate\Support\Facades\Crypt::encryptString($file->id) }}/view"
                                        class="btn btn-outline-primary">Aperçu</a>
                                    <a href="/{{ \Illuminate\Support\Facades\Crypt::encryptString($file->id) }}/download"
                                        class="btn btn-outline-success">Télécharger</a>
                                </div>
                                <div class="pull-left">
                                    <a href="#" onClick="submitSearch(event,'{{ addslashes($file->subject->title) }}')"
                                        class="badge @if(\App\ColorTool::isLight($file->subject->title)) badge-light @else badge-dark @endif"
                                        style="background: #{{  \App\ColorTool::stringToColorCode($file->subject->title)[1] }}">{{
                                        $file->subject->title }}</a>
                                    <a href="#" onClick="submitSearch(event,'{{ addslashes($file->level->title) }}')"
                                        class="badge @if(\App\ColorTool::isLight($file->level->title)) badge-light @else badge-dark @endif"
                                        style="background: #{{  \App\ColorTool::stringToColorCode($file->level->title)[1] }}">{{
                                        $file->level->title }}</a>
                                    <a href="#" onClick="submitSearch(event,'{{ addslashes($file->branch->title) }}')"
                                        class="badge @if(\App\ColorTool::isLight($file->branch->title)) badge-light @else badge-dark @endif"
                                        style="background: #{{  \App\ColorTool::stringToColorCode($file->branch->title)[1] }}">{{
                                        $file->branch->title }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach


            </div>
            @else
            <h4>Aucun fichier.</h4>

            @endif
        </div>

        <div class="col-3">
            <div class="row mx-auto">
                <form action="/search" method="POST" role="search" class="navbar-form pt-3" role="search" style="padding-bottom:1.5rem;width:100%">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <input type="text" class="form-control" style="border-radius:0;" placeholder="Recherche..."
                            name="q">
                        <div class="input-group-btn">
                            <button name="search" class="btn btn-outline-dark search_submit" style="border-radius:0;"
                                type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row">
                <div class="col-12 profile-sidebar">
                    <div class="profile-userpic text-center">
                        <img width="100px" src="{{ Voyager::image(Auth::user()->avatar) }}" class="img-responsive">
                    </div>
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            @if(Auth::user()->first_name && Auth::user()->last_name)
                            {{ Auth::user()->first_name.' '.strtoupper(Auth::user()->last_name) }}
                            @else
                            {{ Auth::user()->name }}
                            @endif
                        </div>
                        @if(Auth::user()->subject && Voyager::can('add_files'))
                        <div class="profile-usertitle-job text-center">
                            @if(!Voyager::can('browse_admin'))
                            Administrateur
                            @else
                            @php 
                                $level = getLevel(Auth::user()->points);
                            @endphp
                            <i class="fa fa-crown"></i> LEVEL {{ $level }}
                            @endif
                        </div> 
                        @php 
                            $nextLevelXP = (int) (($level+1) ** 1.5) * 100;
                            $currentXP = (int) Auth::user()->points;
                            $percentage = (int) ($currentXP*100/$nextLevelXP);
                        @endphp
                        <b>{{ $currentXP }}/{{ $nextLevelXP }} XP</b>
                        <div class="progress levelbar" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: {{  $percentage }}%" aria-valuenow="{{  $percentage }}" aria-valuemin="0" aria-valuemax="100">{{  $percentage  }}%</div>
                        </div>      
                        @endif
                       {{--  <p>Referral Link:
                        <code>{{ Auth::user()->getReferralLink() }}</code></p> --}}
                    </div>
                    <div class="profile-userbuttons">
                        <a class="btn btn-outline-primary btn-sm" href="/settings">Paramètres</a>
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Déconnexion</a>
                    </div>
                </div>
            </div>
            <hr>
            <hr>

            <div class="row">
                <div class="col-12">
                    <h4>Filtrer les résultats</h4>
                    <form method="GET" id="filter">
                        <div class="form-group">
                            <label for="matiere">Matière</label>
                            <select class="selectpicker form-control show-tick" id="subject_id" data-live-search="true"
                                title="Filtrer par">
                                <option value="-1" selected>Tout</option>
                                @foreach ($subjects as $s)
                                @if($s->title)
                                <option value="{{ $s->id }}" data-tokens="{{ $s->abrev }}">{{ $s->title }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="niveau">Niveau</label>
                            <select onchange="showBranch(this);" class="selectpicker form-control show-tick" id="level_id"
                                data-live-search="true" title="Filtrer par">
                                <option value="-1" selected>Tout</option>
                                @foreach ($levels as $group => $glevels)
                                @if($group)
                                <optgroup label="{{ $group }}">
                                    @foreach ($glevels as $level)
                                    <option value="{{ $level->id }}" data-tokens="{{ $group }} {{ $level->title }}">{{
                                        $level->title }}</option>
                                    @endforeach
                                </optgroup>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="branch_selector" style="display:none;">
                            <label for="niveau">Branche</label>
                            <select class="selectpicker form-control show-tick" id="branch_id" data-live-search="true"
                                title="Filtrer par">
                                <option value="-1" selected>Tout</option>
                                @foreach ($branches as $niveau => $branchesN)
                                <optgroup label="@if($niveau == 2) 2ème Année @elseif($niveau == 1) 1ère Année @else Tronc Commun Scientifique @endif">
                                    @foreach ($branchesN as $branch)
                                    <option value="{{ $branch->id }}" data-tokens="{{ $branch->title }}">{{
                                        $branch->title }}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach

                            </select>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-large btn-outline-success"><i class="fa fa-filter"></i>
                                    Filtrer</button>
                                <button type="reset" id="resetFilter" class="btn btn-large btn-outline-primary"><i
                                        class="fa fa-sync"></i> Reset</button>
                            </div>
                        </div>
                        <!--
                    <div class="form-group">
                        <label for="lesson">Lesson</label>
                        <select class="selectpicker form-control show-tick" id="lesson" data-live-search="true" title="Filtrer par">
                            <option data-tokens="ketchup mustard">1</option>
                            <option data-tokens="ketchup mustard">2</option>
                        </select>
                    </div>
                -->
                    </form>
                </div>
            </div>


        </div>
        @endguest
    </div>
</div>
@endsection

@section('add2footer')
<script src="{{ asset('js/isotope.pkgd.min.js') }}" media="all"></script>
<script src="https://carlosroso.com/notyf/notyf.min.js" media="all"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var levels = new Array(9);
    for (var i = 0; i < 9; i++) { // TODO: this should be dynamic
        levels[i] = 5 + i; //This populates the array.  +1 is necessary because arrays are 0 index based and you want to store 1-100 in it, NOT 0-99.
    }

    function showBranch(that) {
        if (jQuery.inArray(parseInt(that.value), levels) == -1) {
            $('#branch_selector').show();
        } else {
            $('#branch_selector').hide();
        }
    }
    // To style only selects with the selectpicker class
    $('.selectpicker').selectpicker();
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var $grid = $('.grid').isotope({
            itemSelector: '.grid-item',
            layoutMode: 'fitRows',
        })
        // Redirect filter:
        $("#filter").submit(function (e) {
            var noResultsContainer = $('#noResultsContainer');
            var isotopeContainer = $(".grid");
            var filterValue = [];
            e.preventDefault();
            var subject_id = $('#subject_id').val();
            var branch_id = $('#branch_id').val();
            var level_id = $('#level_id').val();
            if (subject_id > 0) {
                filterValue.push('.subject_' + subject_id);
            }
            if (branch_id > 0) {
                filterValue.push('.branch_' + branch_id);
            }
            if (level_id > 0) {
                filterValue.push('.level_' + level_id);
            }
            console.log(filterValue);
            // use filterFn if matches value
            $grid.isotope({
                filter: filterValue.join('')
            });
            $("#resetFilter").on("click", function () {
                $grid.isotope({
                    filter: '*'
                });
            });
            if (!$grid.data('isotope').filteredItems.length) {
                noResultsContainer.show();
            } else {
                noResultsContainer.hide();
            }
        });

    });

    function getCurrentScore(scoreCounter) {
        return Number(scoreCounter.html());
    }

    function toggleUp(positive, id) {
        let upArrow = $("svg[class='upArrow'] > path[data_id='" + id + "']");
        let downArrow = $("svg[class='downArrow'] > path[data_id='" + id + "']");
        if (positive) {
            upArrow.css("fill", "#6CC576");
            downArrow.css("fill", "#BBBBBB");
        } else {
            upArrow.css("fill", "#BBBBBB");
            downArrow.css("fill", "#FF586C");
        }
    }
    var isVoting = false;

    function upVote(id) {
        if (isVoting) {
            return;
        }
        isVoting = true;
        $.ajax({
            // async: false, // TODO this is bad, remove it !
            type: 'POST',
            url: '/upvote',
            data: {
                file_id: id,
            },
            success: function (data) {
                var notyf = new Notyf();
                if (data.errors) {
                    //check if response has errors object
                    notyf.alert(data.errors);
                } else {
                    let scoreCounter = $("#scoreCounter[data_id='" + id + "']");
                    var scoreValue = getCurrentScore(scoreCounter);
                    scoreValue++;
                    scoreCounter.html(scoreValue);
                    toggleUp(true, id);
                }
            },
        });
        // Assuming the animation duration is 2 seconds
        setTimeout(function () {
            isVoting = false;
        }, 1000);
    }


    function downVote(id) {
        if (isVoting) {
            return;
        }
        isVoting = true;
        $.ajax({
            type: 'POST',
            url: '/downvote',
            data: {
                file_id: id,
            },
            success: function (data) {
                var notyf = new Notyf();
                if (data.errors) {
                    //check if response has errors object
                    notyf.alert(data.errors);
                } else {
                    // TODO: Something server-side & on success update HTML
                    let scoreCounter = $("#scoreCounter[data_id='" + id + "']");
                    var scoreValue = getCurrentScore(scoreCounter);
                    scoreCounter.html(scoreValue - 1);
                    toggleUp(false, id);
                }
            },
        });
        // Assuming the animation duration is 2 seconds
        setTimeout(function () {
            isVoting = false;
        }, 1000);
    }
</script>
@endsection
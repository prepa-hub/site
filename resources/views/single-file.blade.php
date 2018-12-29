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
            <div class="row file-list    barba-container grid">
                <div class="col-12 grid-item {{ 'subject_'.$file->subject_id }} {{ 'level_'.$file->level_id }} {{ 'branch_'.$file->branch_id }}">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
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
                                    <div class="col-9">
                                        <h5 class="card-title hideOverflow"><a href="/file/{{ $file->id }}">{{ $file->title }}</a></h5>
                                    </div>
                                    <div class="col-3">
                                        <div class="row">
                                            <div class="col-4">{{ $file->views }} <i class="fa fa-eye"></i></div>
                                            <div class="col-4">{{ $file->downloads }} <i class="fa fa-download"></i></div>
                                            <div class="col-4">{{ $file->comments->count() }} <i class="fa fa-comments"></i></div>
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
                                <p class="card-text">Mots Clès: 
                                        @php 
                                            $keywords = explode(',',$file->keywords);
                                        @endphp
                                        @foreach($keywords as $keyword)
                                            <a href="#"  onClick="submitSearch(event,'{{ addslashes($keyword) }}')" target="_blank"><i class="fa fa-tag"></i> {{ $keyword }}</a>
                                        @endforeach
                                    </p>
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
                            <hr>
                            <div class="row container">
                                {!!  $file->description !!}
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-4 offset-md-8">
                                    <a href="/{{ \Illuminate\Support\Facades\Crypt::encryptString($file->id) }}/view"
                                        class="btn btn-outline-primary" target="_blank">Aperçu</a>
                                    <a href="/{{ \Illuminate\Support\Facades\Crypt::encryptString($file->id) }}/download"
                                        class="btn btn-outline-success">Télécharger</a>
                                </div>
                            </div>

                           
                        </div>
                    </div>
                </div>
            </div>
            @comments(['model' => $file])
                @endcomments
        </div>

        <div class="col-3">
            @include('sidebar')
        </div>
        @endguest
    </div>
</div>
@endsection

@section('add2footer')
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-595160497e88b4fd"></script>
<script src="https://carlosroso.com/notyf/notyf.min.js" media="all"></script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
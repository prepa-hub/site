@extends('layouts.layout')
@section('title','Ajouter un fichier')
@section('add2header')
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt"
    crossorigin="anonymous">
<link rel="stylesheet" href="/css/component-dropzone.css" />
@endsection

@section('content')

<div class="container">
    <div class="row" style="height:2rem;"></div>
    <div class="row">
        <div class="col-12">
            <h1>Téléverser un fichier</h1>
            @include('auth.messages')
            <label>Sélectionner un fichier:</label>
            <div id="dropzone" class="dropzone"></div>
            <form method="POST" action="/store">
                <br>
                <div class="form-group">
                    <label for="">Titre</label>
                    <input type="text" name="title" class="form-control" placeholder="Nom du fichier" required>
                </div>
                <div class="form-group">
                    <label for="matiere">Matière</label>
                    <select required class="selectpicker form-control show-tick" id="matiere" data-live-search="true" name="subject" title="Sélectionner...">
                        @foreach ($subjects as $s)
                            @if($s->title)
                                <option value="{{ $s->id }}" data-tokens="{{ $s->abrev }}">{{ $s->title }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="niveau">Niveau</label>
                    <select onchange="showBranch(this);" required class="selectpicker form-control show-tick" id="niveau" data-live-search="true" name="level" title="Sélectionner...">
                        @foreach ($levels as $group => $glevels)
                            @if($group)
                                <optgroup label="{{ $group }}">
                                    @foreach ($glevels as $level)
                                        <option value="{{ $level->id }}" data-tokens="{{ $group }} {{ $level->title }}">{{ $level->title }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="branch_selector" style="display:none;">
                    <label for="niveau">Branche</label>
                    <select class="selectpicker form-control show-tick" id="niveau" data-live-search="true" name="branch" title="Sélectionner...">
                        @foreach ($branches as $niveau => $branchesN)
                                <optgroup label="@if($niveau == 2) 2ème Année @elseif($niveau == 1) 1ère Année @else Tronc Commun Scientifique et Autres @endif">
                                    @foreach ($branchesN as $branch)
                                        <option value="{{ $branch->id }}" @if($branch->id == 8) selected @endif  data-tokens="{{ $branch->title }}">{{ $branch->title }}</option>
                                    @endforeach
                                </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="category">Type de document</label>
                    <select required  class="selectpicker form-control show-tick" id="category" data-live-search="true" name="category" title="Sélectionner...">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" data-tokens="{{ $category->title }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>
                @csrf
                <input type="hidden" value="" name="hash"/>
                <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Publier</button>
            </form>
        </div>
    </div>
    <div class="row" style="height:2rem;"></div>
</div>
</div>
@endsection

@section('add2footer')
<script>
    // To style only selects with the selectpicker class
    $('.selectpicker').selectpicker();
</script>
<script src="/js/dropzone.js"></script>

<script>
Dropzone.autoDiscover = false;
var levels = new Array(9);
for (var i = 0; i < 9; i++) {
    levels[i] = 5 + i; //This populates the array.  +1 is necessary because arrays are 0 index based and you want to store 1-100 in it, NOT 0-99.
}
function isInArray(value, array) {
  return array.indexOf(value) > -1;
}
function showBranch(that) {
    console.log(that.value);
    if (jQuery.inArray(parseInt(that.value), levels) == -1) {
        $('#branch_selector').show();
    } else {
        $('#branch_selector').hide();
    }
}
jQuery(document).ready(function() {
   
  $("#dropzone").dropzone({
    url: "/upload",
    dictDefaultMessage: "Déposer des fichiers ici ou <br> cliquez pour téléverser ...",
    uploadMultiple: false,
    maxFiles:1,
    init: function() {
        this.on("maxfilesexceeded", function(file) {
            this.removeAllFiles();
            this.addFile(file);
        });
        this.on("removeAllFiles", function (file) {
            console.log(file);
            console.log('deleted');
        });
    },
    maxFilesize: 100,
    addRemoveLinks: true,   
    dictRemoveFile: 'Remove file',
    dictFileTooBig: 'Taille de fichier ne doit pas dépasser 100MB',
    timeout: 10000,
    sending: function(file, xhr, formData) {
        formData.append("_token", "{{ csrf_token() }}");
    },
    success: function (file, done) {
        //alert('Success');
        //console.log(file);
        console.log(done);
        $("input[name='title']").val(done.filename);
        $("input[name='hash']").val(done.hash);
    }

  });
});
</script>
@endsection

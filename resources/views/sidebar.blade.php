<div class="addthis_inline_share_toolbox"></div>
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
<hr>
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
@if(isset($showFilter) && $showFilter == true)
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
        <!--<div class="form-group">
            <label for="lesson">Lesson</label>
            <select class="selectpicker form-control show-tick" id="lesson" data-live-search="true" title="Filtrer par">
                <option data-tokens="ketchup mustard">1</option>
                <option data-tokens="ketchup mustard">2</option>
            </select>
        </div> -->
        </form>
    </div>
</div>

@endif
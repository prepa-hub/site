@extends('ar.layouts.frontend')
@section('title', 'باك دوك')
@section('add2header')
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
@endsection

@section('content')
<div class="container">

<div class="row" style="height:2rem;"></div>
<div class="row">
    <div class="col-4">
        <div class="row mx-auto">
            <form class="navbar-form pt-3" role="search" style="padding-bottom:1.5rem;">
                <div class="input-group">
                    <input type="text" class="form-control" style="border-radius:0;" placeholder="ابحث" name="q">
                    <div class="input-group-btn">
                        <button class="btn btn-outline-dark" style="border-radius:0;" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-12 profile-sidebar">
				<div class="profile-userpic text-center">
					<img width="100px" src="https://proxy.duckduckgo.com/iu/?u=http%3A%2F%2Fwww.sammilanimahavidyalaya.org%2Fwp-content%2Fuploads%2F2016%2F01%2Fstudent-icon.png&f=1" class="img-responsive">
				</div>
				<div class="profile-usertitle">
					<div class="profile-usertitle-name">
ذ. أنس المازوني					</div>
					<div class="profile-usertitle-job">
						أستاذ إجتماعيات
					</div>
				</div>
				<div class="profile-userbuttons">
					<button type="button" class="btn btn-outline-primary btn-sm">إعدادات</button>
					<button type="button" class="btn btn-outline-secondary btn-sm">تسجيل الخروج</button>
				</div>
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="filter pt-3">
                        <a href="#" class="btn btn-outline-dark" style="border-radius:0;">الأكثر تحميلا</a>
                        <a href="#" class="btn btn-outline-dark" style="border-radius:0;">الأحدث</a>
                    </div>
                    
                </div>
            </div>
        </div>
        <hr>
        <div class="row file-list">
            @for($i=0;$i<5;$i++)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"> نموذج من الاختبارات شاملة لجميع دروس الفيزياء و الكيمياء السنة الثانية باك</h5>
                            <p class="card-text">من إعداد: <b>أنس المازوني</b></p>
                            <div class="pull-left">
                                <a href="#" class="btn btn-outline-primary">معاينة</a>
                                <a href="#" class="btn btn-outline-success">تحميل</a>
                            </div>
                            <div class="pull-right">
                                <a href="#" class="badge badge-danger">الفيزياء و الكيمياء</a>
                                <a href="#" class="badge badge-warning">السنة الثانية باك</a>
                            </div>
                                    
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
</div>
@endsection

@section('add2footer')
@endsection
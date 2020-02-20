<?php
    use \App\Http\Controllers\co_ordinate;
?>
@extends('co-ordinates/cod_layout')

@section('title','View Result')

@section('my-content')
<div class="container-fluid">
    <div class="mb-0 pt-2 card new-shadow-sm">
         <a href="{{url('cindex')}}" class="text-right text-dark px-2">
            <i data-feather="x-circle" id="close-btn" height="20px"></i>
        </a> 
        <h2 class="font-weight-normal text-dark text-center">{{ucfirst($einfo['ename'])}}</h2>
        <h6 class="font-weight-normal text-dark text-center">{{ucfirst(Session::get('clgname'))}}</h6>
        <h6 class="font-weight-normal text-dark text-center">
            <span class="font-weight-bold badge badge-soft-dark px-3 badge-pill">{{date('d/m/Y',strtotime($einfo['edate']))}}</span>
            <span class="ml-1 font-weight-bold  badge badge-soft-dark px-4 badge-pill">{{date('l',strtotime($einfo['edate']))}}</span>
        </h6>
        <hr class=" my-1">
    </div>
<div class="mt-0">
    <div class="card mb-0 rounded-sm">
        <div class="card-body py-2">
            <div class="h5 d-flex align-items-center">
                <i data-feather="award" class="icon-dual-success"></i>
                <span class="ml-1">Top 3 Winner Candidate</span>
            </div>
        </div>
        <hr class=" my-1">
    </div>
    
    <div class="card new-shadow-sm" style="max-height: 350px;">
        <div class="card-body overflow-auto my-scroll">
            <div class="table-responsive overflow-auto my-scroll">
                <table class="table table-hover table-nowrap mb-0">
                    <thead style="background-color:#1ce1ac40;color:#000;">
                                        <tr>
                                            <th scope="col">Rank</th>
                                            <th scope="col">EID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Class</th>
                                            <th scope="col">Division</th>
                                        </tr>
                                    </thead>
                                    <?php
                                        $r1=\DB::table('tblparticipant')->select('senrl')->where([['eid',$einfo['eid']],['rank',1]])->first();
                                        $sinfo=co_ordinate::studinfo($r1->senrl);
                                    ?>
                                    <tbody class="text-dark">
                                        <tr>
                                            <th scope="row">
                                                <img src="{{asset('assets/images/svg-icons/student-dash/winner/1.svg')}}" height="25px" alt="1">
                                            </th>
                                            <td>{{$r1->senrl}}</td>
                                            <td>{{ucfirst($sinfo['sname'])}}</td>
                                            <td>{{ucfirst($sinfo['class'])}}</td>
                                            <td>{{$sinfo['division']}}</td>
                                        </tr>
                                    <?php
                                        $r2=\DB::table('tblparticipant')->select('senrl')->where([['eid',$einfo['eid']],['rank',2]])->first();
                                    ?>
                                        @if($r2)
                                        <?php
                                            $sinfo=co_ordinate::studinfo($r2->senrl);
                                        ?>
                                        <tr>
                                            <th scope="row">
                                                <img src="{{asset('assets/images/svg-icons/student-dash/winner/2.svg')}}" height="25px" alt="2">
                                            </th>
                                            <td>{{$r2->senrl}}</td>
                                            <td>{{ucfirst($sinfo['sname'])}}</td>
                                            <td>{{ucfirst($sinfo['class'])}}</td>
                                            <td>{{$sinfo['division']}}</td>
                                        </tr>
                                        @endif
                                        <?php
                                        $r3=\DB::table('tblparticipant')->select('senrl')->where([['eid',$einfo['eid']],['rank',3]])->first();
                                        ?>
                                        @if($r3)
                                        <?php
                                            $sinfo=co_ordinate::studinfo($r3->senrl);
                                        ?>
                                        <tr>
                                            <th scope="row">
                                                <img src="{{asset('assets/images/svg-icons/student-dash/winner/2.svg')}}" height="25px" alt="2">
                                            </th>
                                            <td>{{$r3->senrl}}</td>
                                            <td>{{ucfirst($sinfo['sname'])}}</td>
                                            <td>{{ucfirst($sinfo['class'])}}</td>
                                            <td>{{$sinfo['division']}}</td>
                                        </tr>
                                        @endif
                                </tbody>
                        </table>
                </div>
        </div>
    </div>
                        
</div>
@if($participant > 0)
    <div class="card mb-0 rounded-sm">
        <div class="card-body py-2 d-flex justify-content-between align-items-center">
            <div class="h5 d-flex align-items-center">
                <i data-feather="users" class="icon-dual-info"></i>
                <span class="ml-1">Other Candidates</span>
            </div>
            <div class="h6 d-flex align-items-center">
                <span>Total</span>
                <span class="ml-2">{{$participant}}</span>
            </div>
        </div>
        <hr class=" my-1">
    </div>
    <?php
        $parti=co_ordinate::participant($einfo['eid']);
    ?>
    <div class="card new-shadow-sm" style="max-height: 350px;">
        <div class="card-body overflow-auto my-scroll">
            <div class="table-responsive overflow-auto my-scroll">
                <table class="table table-hover table-nowrap mb-0">
                    <thead style="background-color:#25c2e340;color:#000;">
                        <tr>
                            <th scope="col">EID</th>
                            <th scope="col">Name</t h>
                            <th scope="col">Class</th>
                            <th scope="col">Division</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                    @foreach($parti as $participant)
                    <?php $sinfo=co_ordinate::studinfo($participant['senrl']);?>
                        <tr>
                            <td>{{$participant['senrl']}}</td>
                            <td>{{ucfirst($sinfo['sname'])}}</td>
                            <td>{{ucfirst($sinfo['class'])}}</td>
                            <td>{{$sinfo['division']}}</td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div> <!-- end table-responsive-->
        </div> <!-- end card-body-->
    </div>
@endif
</div>
@endsection

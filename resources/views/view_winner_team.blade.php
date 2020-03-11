
@extends('stud_layout')

@section('title','Winner List')
@section('head-tag-links')
<style>

</style>
@endsection
@section('my-content')
<div class="container-fluid my-5">
    <div class="mb-0 pt-2 card new-shadow-sm py-4">

        <span class="h3 my-0 font-weight-normal text-dark text-center">
        {{ucfirst($tc['tname'])}}
        </span>
        <h6 class="font-weight-normal text-dark text-center">
           {{Session::get('clgname')}}
        </h6>
        <hr class="my-0">
    </div>
    <div class="card new-shadow-sm" >
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-nowrap mb-0" >
                    <thead style="background-color:#1ce1ac40;color:#000;">
                    
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">EID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Class</th>
                            <th scope="col">Division</th>                
                        </tr>
                    </thead>
                    <?php 
                        $players=explode('-',$tc['senrl']);
                        $a=0;
                    ?>
                    <tbody class="text-dark">
                    @foreach($players as $player)
                        @if($player)
                        <?php 
                            $sinfo=App\tblstudent::select('senrl','sname','class','division')->where('senrl',$player)->first();
                            $a++; 
                        ?> 
                        <tr>
                            <td>{{$a}}</td>
                            <td>{{$sinfo['senrl']}}</td>
                            <td>{{ucfirst($sinfo['sname'])}}</td>
                            <td>{{strtoupper($sinfo['class'])}}</td>
                            <td>{{$sinfo['division']}}</td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


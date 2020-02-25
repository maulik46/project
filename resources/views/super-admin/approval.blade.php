@extends('super-admin/s_admin_layout')

@section('title','Delete Approval')

@section('head-tag-links')
<style>
    #labels span:nth-child(odd){
        border-radius:6rem 0px 0px 6rem!important;
    }
    #labels span:nth-child(even){
        border-radius:0px 6rem 6rem 0px!important;
    }
    .card-text div span{
        font-weight:bold;
        color:var(--dark);
        margin-right: 6px;
    }
    
</style>
@endsection
@section('my-content')
<div class="container-fluid">
    <div class="card mb-0 new-shadow-sm">
        <a href="{{url('sindex')}}" class="text-dark text-right p-2" id="close-btn">
            <i data-feather="x-circle" height="20px"></i>
        </a>
        <div class="text-dark d-flex align-items-center justify-content-center p-1">
            <i data-feather="calendar"></i>
            <span class="h4 text-dark ml-2">Approve Deleted Event</span>
        </div>
    </div>
    <?php $a=0;?>
    @foreach($delevnt as $del)
    <?php $a=1;?>
    <div class="card mb-0 mt-1 new-shadow-sm">
        <div class="card-body py-2">
           
            <div class="row justify-content-between align-items-center mx-0">
                <span class="text-danger font-weight-bold font-size-16">{{ucfirst($del['ename'])}} competition</span>
                <div id="labels">
                    <span class="badge badge-primary px-3 badge-pill  my-1 rounded-0" style="margin-right:-5px;">
                       Event-date
                    </span>
                    <span class="badge badge-soft-primary px-3 badge-pill  my-1 rounded-0">
                        {{date('d-m-Y',strtotime($del['edate']))}}
                    </span>
                </div>
            </div>
            <hr class="my-0">
            <div class="row justify-content-between align-items-end">
                <div class="card-text col-xl-8 col-md-6 c col-12">
                    <div>
                        <span>Event Co-ordinator</span>  {{ucfirst($del['cname'])}}
                    </div>
                    <div>
                        <span>Event-Type</span>  {{ucfirst($del['category'])}}
                    </div>
                    <div>
                        <span>Reason</span> {{ucfirst($del['reason'])}}
                    </div>
                    
                </div>  
                <div class="col-xl-4 col-md-6  col-12 d-flex align-items-center justify-content-end">
                    <a href="" onclick="return confirm('<?php echo ucfirst($del['ename'])?>','<?php echo encrypt($del['eid'])?>','del')" class="m-1 btn btn-block btn-danger btn-sm  btn-rounded float-right font-weight-bold px-3" >
                        <span>Delete Event</span>    
                    </a>
                    <a href="#" onclick="return confirm('<?php echo ucfirst($del['ename'])?>','<?php echo encrypt($del['eid'])?>','cal')" class="m-1 btn btn-block btn-default btn-sm  btn-rounded float-right font-weight-bold px-3 text-danger cancel-btn" style="border:2px solid var(--danger);">
                        <span>Cancel Request</span>    
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @if($a==0)
    <div class="mt-2 py-5 card text-center new-shadow-sm">
        <div class="font-weight-bold font-size-16">No events are available to approve!!</div>
    </div>
    @endif
</div>
@endsection
@section('extra-scripts')
<script src="{{asset('assets/js/sweetalert2.min.js')}}"></script>
<script>
    function confirm(ename,eid,val)
    {
        var y=0;
        if(val=="del")
        {
            var msg= "You won't delete Event <b>" + ename + " </b> !!";
            var btn='Yes, delete it!';
        }
        else
        {
            var msg= "You won't reject request for delete Event <b>" + ename + " </b> !!";
            var btn='Yes, cancel it!';
        }
        Swal.fire({
        title: 'Are you sure?',
        html:msg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: btn,
        }).then((result) => {
        if (result.value) {
            window.location.href = '<?php echo url('/approval/confrim_del').'/' ?>'+eid+'/'+val;
        }
        })
        
        return false;
    }
</script>

@endsection

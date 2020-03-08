@extends('system/system_layout')
@section('title','Read Requests')

@section('my-content')
<div class="container-fluid mt-2">
    <div class="mt-4 rounded-lg p-3 px-1 px-sm-2" style="border:1px solid #e2e7f1;">
    <div>
        <div class="d-flex justify-content-center align-items-start align-items-sm-center flex-column flex wrap">
            <h5>
            Sutex bank college of computer applications & science
            </h5>
            <div class="d-flex flex-sm-row flex-column">
                <span class="badge badge-pill badge-soft-primary pl-2 pr-3 m-1">
                <i data-feather="calendar" height="14px"></i>
                12/12/2019
                </span>
                <span class="badge badge-pill badge-soft-info pl-2 pr-3 m-1">
                <i data-feather="mail" height="14px"></i>
                yashparmar@gmail.com
                </span>
                <span class="badge badge-pill badge-soft-dark pl-2 pr-3 m-1">
                <i data-feather="phone" height="14px"></i>
                9284984923
                </span>
            </div>
        </div>
        <hr>
        <div class="text-muted">
        Hello..
        <br><br>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Alias perferendis, repellendus doloribus ipsum cupiditate atque consectetur nostrum repellat incidunt vero voluptate rem hic perspiciatis impedit explicabo nobis, veritatis consequuntur architecto.
        <br><br>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Alias perferendis, repellendus doloribus ipsum cupiditate atque consectetur nostrum repellat incidunt vero voluptate rem hic perspiciatis impedit explicabo nobis, veritatis consequuntur architecto.
        <br><br>
            <div class="text-muted font-weight-bold">
                <i data-feather="map-pin" height="18px"></i>Address
                <div class="m-2">
                Lorem ipsum dolor sit amet consectetur adipisicing elit
                <br>
                <span>City name</span>
                </div>
            </div>
            <div class="text-right font-weight-bold text-dark m-3">
            <h6>By Yash Parmar</h6>
            </div>
        </div>
    </div>
    </div>
    <a href="#" class="mt-2 btn btn-success font-weight-bold rounded-sm px-3 new-shadow-sm hover-me-sm">
        Accept Request
        <i data-feather="check-square" height="18px"></i>
    </a>
    <a href="#" class="mt-2 btn btn-danger font-weight-bold rounded-sm px-3 new-shadow-sm hover-me-sm">
        Reject
        <i data-feather="x-circle" height="18px"></i>
    </a>
</div>
@endsection


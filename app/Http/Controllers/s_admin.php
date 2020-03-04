<?php

namespace App\Http\Controllers;
date_default_timezone_set("Asia/Kolkata"); 
use Illuminate\Http\Request;
use validator;
use App\tblevent;
use App\notice;
use App\tblstudent;
use App\tblcoordinaters;
use App\participant;
use App\log;
use App\admin;
use Cookie;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportStudent;
class s_admin extends Controller
{
    public function checklogin(Request $req)
    {
        $login_details = admin::where([
            ['email', '=', $req->auser],
            ['pass', '=', $req->password]
        ])
        ->count();
        if ($login_details==1) 
        {
            $admin=admin::where([['pass', $req->password],['email',$req->auser]])->first();
           $clgname=\DB::table('tblcolleges')->select('clgname')->where('clgcode',$admin['clgcode'])->first();
            session()->put('aid', $admin['aid']);
            session()->put('clgcode', $admin['clgcode']);
            session()->put('clgname',$clgname->clgname);
            session()->put('aname',$admin['name']);
            session()->put('email',$admin['email']);  
            session()->put('adminprofile',$admin['profilepic']);  
            session()->put('mobile',$admin['mobile']);
            $log=new log;
            $log->uid=Session::get('aid');
            $log->action_on="login";
            $log->action_type="login";
            $log->time=time();
            $log->utype="admin";
            $log->ip_add=$_SERVER['REMOTE_ADDR'];
            $log->save();
            // print_r(session()->all());
            return redirect(url('sindex'));
        } 
        else
        {
            return back()->with('error','Invalid email address or Password');
        }
    }
    public function logout()
    {
        $log=new log;
        $log->uid=Session::get('aid');
        $log->action_on="logout";
        $log->action_type="logout";
        $log->time=time();
        $log->utype="admin";
        $log->ip_add=$_SERVER['REMOTE_ADDR'];
        $log->save();
        Session::flush(); 
        return redirect(url('/slogin'));
    }
    public function sindex()
    {
        $events=tblevent::where([['clgcode',Session::get('clgcode')]
            ])->orderby('edate','desc')->get()->toarray();
        $cod=tblcoordinaters::where('clgcode',Session::get('clgcode'))->get();
        return view('super-admin/index',['events'=>$events,'cods'=>$cod]);
    }
     public function send_notice(Request $req)
    {
       $topic=$req->title;
       $message=$req->message;
       $filename="";
       $fname="";
       $receiver=$req->nfor;
       if($file=$req->file('attachment'))
       {
           $req->validate([
               'attachment' => 'max:50',
           ],
       [
           'attachment.max'=>"The file size should be less then 50 Kb"
       ]);
           foreach($file as $att)
           {
               $destinationPath=public_path('attachment/');
               $filename=time().$att->getClientOriginalName();
               $att->move($destinationPath,$filename);
               $fname.=$filename.";";
           }
       }
        $cod=app('App\Http\Controllers\co_ordinate')->notice($topic,Session::get('aname'),'Admin',$receiver,$message,$fname);
        session()->flash('success', 'Notice send successfully');
       $log=new log;
       $log->uid=Session::get('aid');
       $log->action_on="Notice";
       $log->action_type="insert";
       $log->time=time();
       $log->utype="admin";
       $log->ip_add=$_SERVER['REMOTE_ADDR'];
       $log->save();
       return redirect(url('sindex'));
    }
    public function update_pass(Request $req)
    {
       $c=admin::where([['aid',Session::get('aid')],['pass',$req->current_pass]])->count();
       if($c==1)
       {
           if($req->npass == $req->cpass)
           {
               admin::where('aid',Session::get('aid'))->update(['pass'=>$req->npass]);
           }
           else{
               session()->flash('error', 'Newpassword and confirm password not match');
               return redirect()->back();
           }
       }
       else
       {
           session()->flash('error', 'Invalid password');
           return redirect()->back();
           
       }
       $log=new log;
       $log->uid=Session::get('aid');
       $log->action_on="password ";
       $log->action_type="update";
       $log->time=time();
       $log->utype="admin";
       $log->ip_add=$_SERVER['REMOTE_ADDR'];
       $log->save();
       session()->flash('success', 'Password updated successfully..!');
       return redirect(url('sindex'));
    }
    public function last_noti(Request $req)
    {
        $las_notice=$req->lastnote;
        admin::where('aid',Session::get('aid'))->update(['last_noti'=>$las_notice]);
        return response()->json(array('msg'=> $las_notice),200);
    }
    public function approval()
    {
        $apl=tblevent::join('tblapproval','tblapproval.eid','=','tblevents.eid')
        ->join('tblcoordinaters','tblcoordinaters.cid','=','tblapproval.cid')->where('tblevents.clgcode',Session::get('clgcode'))->get()->toarray();
        return view('super-admin/approval',['delevnt'=>$apl]);
    }
    public function con_del($eid,$y)
    {
        $e_id=decrypt($eid);
        
        if($y=="del")
        {
            $msg_info=tblevent::join('tblapproval','tblapproval.eid','tblevents.eid')->where('tblevents.eid',$e_id)->first();
            $message="Event Name    <b>:".ucfirst($msg_info['ename'])."</b><br>Reason       <b>:".ucfirst($msg_info['reason']);
            
            
            $notice=app('App\Http\Controllers\co_ordinate')->notice('Event Canceled','System','System','student-coordinator',$message,"");
            
            $tblp=participant::select('senrl')->where('eid',$e_id)->get()->toArray();
                
                foreach($tblp as $p)
                {
                    
                        $senrl=explode('-', $p['senrl']);

                        foreach ($senrl as $enrl) {
                            if ($enrl) {
                                
                                $tbls=tblstudent::select('sname', 'email')->where('senrl', $enrl)->get()->first();
                                $data=array('name'=>'Cancel Event','edate'=>$msg_info['edate'],'ename'=>$msg_info['ename'],'reason'=>$msg_info['reason'],'reciever'=>$tbls['sname']);
                                $this->mail($tbls['sname'], trim($tbls['email']), $data);
                            }
                        }
                }
            $events=tblevent::where('eid', $e_id)->delete();
            $del_part=participant::where('eid',$e_id)->delete();
            
        }
        $del_event=\DB::table('tblapproval')->where('eid',$e_id)->delete();
        return back();
    }
     public function mail($to_name,$to_email,$data)
    {
        \Mail::send('del_email',$data,function($message) use ($to_name, $to_email){
                $message->to($to_email)->replyTo("eventoitsol@gmail.com",$name=null)
                ->from("eventoitsol@gmail.com", $name = "Evento")
                ->subject("Event cancelled")->bcc($to_email);
            });
    }
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required'
        ]);
        Excel::import(new ImportStudent, request()->file('import_file'));
        return back()->with('success', 'Student record inserted successfully.');
    }
    
    public function checkenrl(Request $req)
    {
        $enrl=$req->enrl;
        $c=tblstudent::where('senrl',$enrl)->count();
        if($c>0)
        {
            return response()->json(array('msg'=> "This Enrollment number already available"),200);
        }
        return response()->json(array('msg'=> ""),200);
    }
    public function checkemail(Request $req)
    {
        $email=$req->email;
        $c=tblstudent::where('email',$email)->count();
        if($c>0)
        {
            return response()->json(array('msg'=> "This email addess already taken"),200);
        }
        return response()->json(array('msg'=> ""),200);
    }
    public function checkmobile(Request $req)
    {
        $mobile=$req->mobile;
        $c=tblstudent::where('mobile',$mobile)->count();
        if($c>0)
        {
            return response()->json(array('msg'=> "This Mobile number already taken"),200);
        }
        return response()->json(array('msg'=> ""),200);
    }
    public function checkrno(Request $req)
    {
        $rno=$req->rno;
        $clas=$req->clas;
        $c=tblstudent::where([['rno',$rno],['class',$clas]])->count();
        if($c>0)
        {
            return response()->json(array('msg'=> "This roll no already available for this class"),200);
        }
        return response()->json(array('msg'=> ""),200);
    }
    public function studinsrt(Request $req)
    {
        $stud=new tblstudent;
        $stud->senrl=$req->enrl;
        $stud->sname=$req->name;
        $stud->rno=$req->rno;
        $stud->clgcode=Session::get('clgcode');
        $stud->dob=date('Y-m-d',strtotime($req->dob));
        $stud->class=$req->clas;
        $stud->division=$req->div;
        $stud->email=$req->email;
        $stud->mobile=$req->mobile;
        $stud->address=$req->add;
        $stud->gender=$req->gen;
        $stud->save();
        return back()->with('success', 'Student record inserted successfully.');
    }
    public function view_students()
    {
        // Session::get('clgname')
        
        $stud = tblstudent::where('clgcode',Session::get('clgcode'))->get();
        // return $stud;
        // exit;
        $alldata = ['stud' => $stud];
        return view('super-admin/view_students', $alldata);

    }
    public function update_stud($en){
        $en = decrypt($en);
        $table = tblstudent::where('senrl', $en)->get()->first();
        return view('super-admin/update_student', ['stud_data' => $table]);

    }
    public function action_update_stud(Request $req,$en){
        $en = decrypt($en);        
        $senrl=$req->s_enrl;
        $sname=$req->s_name;
        $rno=$req->s_rollno;
        $dob=$req->s_dob;
        $class=$req->s_class;
        $division=$req->s_division;
        $email=$req->s_email;
        $mobile=$req->s_contact;
        $address=$req->s_address;
        $gender=$req->s_gender;

        $update = tblstudent::where('senrl', $en)->update(['sname' => $sname, 'rno' => $rno,'dob' => $dob,'class' => $class,'division' => $division,'email' => $email,'mobile' => $mobile,'address' => $address,'gender' => $gender]);
        if ($update) {
            session()->flash('msg', 'Record has been updated!!');
        }

        return redirect(url('/view_students'));

    }
      public function filterlog(Request $req)
    {
        $cid=$req->cid;
        $sdate=$req->sdate;
        $ldate=$req->ldate;
        if($cid=="all")
        {
            $cid='%%';
        }
        if($ldate=="")
        {
            $ldate=time();
        }
        else{
            $ldate=strtotime("+1 day", strtotime($ldate));
        }
        if($sdate=="")
        {
            $sdate=0;
        }
        else{
            $sdate=strtotime($sdate);
        }
        $logs=log::join('tblcoordinaters','tblcoordinaters.cid','tbllog.uid')
        ->where([['tblcoordinaters.clgcode',Session::get('clgcode')],
        ['tbllog.utype','co-ordinatore'],
        ['tblcoordinaters.cid','LIKE',$cid],
        ['tbllog.time','>=',$sdate],['tbllog.time','<=',$ldate]])->orderby('time','desc')->get()->toarray();
        $msg="";
        foreach($logs as $log){
        $msg.='<div class="card mb-0 mt-3 new-shadow-sm">
            <div class="card-body py-2">
                <div class="row justify-content-between mx-0">
                    <div>
                    <span class="badge badge-info px-3 badge-pill  my-1">'.date('d-m-Y',$log['time']).'</span>
                    <span class="badge badge-danger px-3 badge-pill  my-1">'.date('h:m:s A',$log['time']).'</span>
                    <span class="badge badge-soft-primary px-3 badge-pill  my-1">By '.ucfirst($log['cname']).'</span>
                    </div>
                    <div id="action" class="row justify-content-between mx-0">
                        <div class="mr-1">
                            <span class="badge badge-dark px-3 rounded-0  my-1" style="margin-right:-5px;">Action on</span>
                            <span class="badge badge-soft-dark px-3 rounded-0">'.ucfirst($log['action_on']).'</span>
                        </div>
                        <div class="ml-1">
                            <span class="badge badge-dark px-3 rounded-0  my-1" style="margin-right:-5px;">Action</span>
                            <span class="badge badge-soft-dark px-3 rounded-0">'.ucfirst($log['action_type']).'</span>
                        </div>
                    </div>
                </div>
                <div class="card-text">
                    
                    <div class="text-dark font-size-15 font-weight-bold">Location changed to lab 3 of php quiz eventLocation changed to lab 3 of php quiz</div>
                    <span class="text-muted more">Location changed to lab 3</span>
                    <div class="float-right">
                        <span class="font-weight-bold">IP</span>
                        <span class="badge badge-soft-dark badge-pill px-2">'.$log['ip_add'].'</span>
                    </div>
                </div>
            </div>
            
        </div>';
        }
        $msg.='<div class="mt-3">'.$logs->links().'</div>';
        return response()->json(array('msg'=>$msg),200);
    }
    public function otp_mail($to_name,$to_email,$data)
    {
        \Mail::send('email',$data,function($message) use ($to_name, $to_email){
                $message->to($to_email)->replyTo("eventoitsol@gmail.com",$name=null)
                ->from("eventoitsol@gmail.com", $name = "Evento")
                ->subject("OTP Athentication")->bcc($to_email);
            });
    }
    public function send_otp(Request $req)
     {
         if ($req->ajax()) {
             $rand_num=rand(111111, 999999);
             if($req->get('cuser_resend'))
             {
                $cuser=$req->get('cuser_resend');
                session()->put('email_check',1);
             }
             else
             {
                $cuser=$req->get('cuser');
             }
             session()->put('otps', $rand_num);
             $message="<br/>---<br/> Thanks For visiting Evento ";
             $data=array('name'=>'OTP :'.Session::get('otps'),'body'=>$message);
             $tbla=admin::where('email', $cuser)->get()->first();
             if ($tbla) {
                 if (Session::get('email_check')==1) {
                    //  $this->otp_mail($tbla['name'], $tbla['email'], $data);
                     session()->put('email_check',0);
                 }
                 $data=Session::get('otps');
             }
             else{
                 $data="Invalid Email Id..";
             }
            
             echo json_encode($data);
         }
     }
     public function confirm_pass(Request $req)
     {
        return view("super-admin/confirm_pass",['email'=>$req->cuser]);
     }
     public function change_pass(Request $req,$email)
     {
        $email=decrypt($email);
        //echo $email;
        $pass=$req->password;
        $cpass=$req->cpassword;
        if($pass==$cpass)
        {
            $tblc=admin::where('email',$email)
            ->update(['pass' =>$pass]);
            if($tblc)
            {
                session()->flash("success","Your Password Successfully Changed...");
            }
            return redirect(url('/slogin'));
        }
        else
        {
            return redirect()->back();
        }
     }
    public function timers(Request $req)
    {
        if($req->ajax())
        {
            if(Session::get('otps')==$req->get('otpcode'))
            {
                Session::forget('otps');
                $data="";
                echo json_encode($data);
            }
        }
        
    }
    public function new_cod_add(Request $req)
    {
        if (isset($req->avatar)) {
            $avatar=$req->avatar;
        }
        else
        {
            $file=$req->file('photo-upload');
            $destinationPath=public_path('profile_pic/');
            $filename=time().$file->getClientOriginalName();
            $file->move($destinationPath, $filename);
            $avatar=$filename;
        }
        $tblc=tblcoordinaters::insert(['clgcode'=>Session::get('clgcode'),'cname'=>strtolower($req->cname),'email'=>$req->email,
        'password'=>$req->pass,'phoneno'=>$req->cno,'category'=>$req->category,'pro_pic'=>$avatar] );
        if($tblc)
        {
            //echo "new co create";
            $to_name=strtoupper($req->cname);
            $to_email=$req->email;
            $message=" <table border=1 style='padding:10px'> <tr style='background-color:#e6e6e6'><td style='padding:5px'> User Name </td><td style='padding:5px'> Password </td></tr>  <tr><td style='padding:5px'>".$req->email."</td> <td style='padding:5px'>".$req->pass."</td></tr>    </table> ";
            $data=array('name'=>'Welcome to Evento ','body'=>$message);
            \Mail::send('email',$data,function($message) use ($to_name, $to_email){
                $message->to($to_email)->replyTo("eventoitsol@gmail.com",$name=null)
                ->from("eventoitsol@gmail.com", $name = "Evento")
                ->subject("Co-ordinaters login")->bcc($to_email);
            });
            session()->flash('success','New Co-ordinater Created...!');
        }
        return redirect(url('/sindex'));
       
    }
    public function err(Request $req){
        $email = $req->email;
        $cno=$req->cno;
        $email_check=tblcoordinaters::where([['clgcode',Session::get('clgcode')],['email',$email]
        ])->count();
        $cno_check=tblcoordinaters::where([['clgcode',Session::get('clgcode')],['phoneno',$cno]
        ])->count();
            return response()->json(array('email'=> $email_check,'phoneno'=> $cno_check),200);
     }
    public function updateprofile(Request $req)
    {
        $aname=$req->aname;
        $aid=$req->aid;
        $aemail=$req->aemail;
        $mobile=$req->mobile;
        $rec=admin::where('aid',$aid)->update(['name'=>$aname,'email'=>$aemail,'mobile'=>$mobile]);
        if($rec>0)
        {
            session()->put('aname',$aname);
            session()->put('email',$aemail);
            session()->put('mobile',$mobile);
            session()->flash('success', 'Profile updated successfully');
        }
        return back();
    }
    public function event_info($id)
     {      $id=decrypt($id);
            $einfo=tblevent::where('eid',$id)->first();
            return view("super-admin/event_info",['einfo'=>$einfo]);
     }
     public function view_result($eid)
     {
         $participant=participant::where([['eid',decrypt($eid)],['rank','p']])->count();
         $event=tblevent::select('eid','ename','edate')->where('eid',decrypt($eid))->first();
         return view('super-admin/view_result',['participant'=>$participant],['einfo'=>$event]);
     }
     public function view_can($id)
    {
        $id=decrypt($id);
        $participate=participant::select('senrl','tname')->where('eid',$id)->get()->toarray();
        $einfo=tblevent::select('eid','ename','e_type','edate')->where('eid',$id)->first()->toarray();
        return view('super-admin/view_candidates',['participate'=>$participate],['einfo'=>$einfo]);
    }
}

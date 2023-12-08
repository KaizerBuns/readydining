<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Restaurant;
use App\Queue;
use App\Provider;
use App\User;

class HomeController extends Controller
{
    public $system_alert = array(
        'account_created' => array('class' => 'alert-success', 'text' => 'Your account has been successfully created.', 'icon' => 'fa-check'),
        'account_confirmed' => array('class' => 'alert-success', 'text' => 'Your account has been confirmed.', 'icon' => 'fa-check'),
        'account_notexists' => array('class' => 'alert-warning', 'text' => 'The account does not exists.', 'icon' => 'fa-check'),
        'confirm_sent' => array('class' => 'alert-success', 'text' => 'An email confirmation link has been sent. Please click on the link to confirm your account.', 'icon' => 'fa-check'),
        'saved' => array('class' => 'alert-success', 'text' => 'Your changes have been updated.', 'icon' => 'fa-check'),
        'deleted' => array('class' => 'alert-success', 'text' => 'The selected item(s) have been deleted.', 'icon' => 'fa-check'),
        'loggedin' => array('class' => 'alert-success', 'text' => 'Welcome back, you have been logged in automatically.', 'icon' => 'fa-check'),
        'account_welcome' => array('class' => 'alert-success', 'text' => 'Welcome, please update your Username and your account details.', 'icon' => 'fa-check'),
        'code_sent' => array('class' => 'alert-success', 'text' => 'Code has been sent to your SMS Number.', 'icon' => 'fa-check'),
        'login_failed' => array('class' => 'alert-warning', 'text' => 'Your email / password are incorrect please try again.', 'icon' => 'fa-ban'),
        'unknown_error' => array('class' => 'alert-danger', 'text' => 'An unknown error occurred please try again.', 'icon' => 'fa-ban'),
        'session_timeout' => array('class' => 'alert-warning', 'text' => 'Your session timed out, please login again.', 'icon' => 'fa-warning'),
        'logout' => array('class' => 'alert-success', 'text' => 'You have successfully logged out.', 'icon' => 'fa-warning'),
        'notfound' => array('class' => 'alert-warning', 'text' => 'The ID / Object you are trying access does not exists.', 'icon' => 'fa-warning'),
        'corruptdata' => array('class' => 'alert-warning', 'text' => 'The submitted data has been modified/corrupted please try again.', 'icon' => 'fa-warning'),
        'duplicate' => array('class' => 'alert-warning', 'text' => 'The submitted data is a duplicate please try again.', 'icon' => 'fa-warning'),
        'forgotpasswd' => array('class' => 'alert-warning', 'text' => 'Please reset your account password.', 'icon' => 'fa-warning'),
        'passwderr' => array('class' => 'alert-warning', 'text' => "Confirm password doesn't match password", 'icon' => 'fa-warning'),
        'sendpasswd' => array('class' => 'alert-success', 'text' => 'An email has been sent to your account.', 'icon' => 'fa-check'),
        'denied' => array('class' => 'alert-warning', 'text' => 'Your account does not have access to this section', 'icon' => 'fa-warning'),   
    );

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->middleware('auth');
        $this->user = \Auth::user();
        $this->code_list = array(4678, 9012, 8758, 1739, 1897);
        view()->share('user', $this->user);
        view()->share('request', $this->request);
        if($this->request->input('msg')) {
            view()->share('alert', $this->system_alert[$this->request->input('msg')]);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $queues = Queue::get_all($this->user);
        return view('home', ['queues' => $queues]);
    }

    public function new_queue() 
    {
        $events = Restaurant::get_all();
        return view('queue', ['events' => $events]);   
    }

    public function save_queue() {
        $queue = new Queue();
        $queue->user_id = $this->user->id;
        $queue->fill($_REQUEST['queue']);
        $queue->save();
        return redirect('/dashboard/?msg=saved');
    }

     public function update_queue() {
        $id = $this->request->input('id');
        $queue = Queue::find($id);

        if(!$queue) {
            return '0';
        }
        
        $queue->description = $this->request->input('description');
        $queue->save();
        return redirect('/dashboard/?msg=saved');
    }

    public function delete_queue() {
        $id = $this->request->input('id');
        $queue = Queue::find($id);

        if(!$queue) {
            return redirect('/dashboard/?msg=notfound');            
        }

        $queue->deleted = 1;
        $queue->deleted_at = date("Y-m-d H:i:s");
        $queue->save();

        return redirect('/dashboard/?msg=deleted');   
    }

    public function reset_queue() {
        $id = $this->request->input('id');
        $queue = Queue::find($id);

        if(!$queue) {
            return redirect('/dashboard/?msg=notfound');            
        }

        $queue->success = 0;
        $queue->alert = 0;
        $queue->error = 0;
        $queue->alerted_at = '0000-00-00 00:00:00';
        $queue->availability = 'pending';
        $queue->save();

        return redirect('/dashboard/?msg=saved');   
    }

    public function profile_view() {
        $providers = Provider::where(array('type' => 'phone'))->get();
        return view('profile', ['profile' => $this->user, 'providers' => $providers]);   
    }

    public function profile_save() {
        $redirect_url = '/dashboard?msg=saved';
        $this->user->fill($_REQUEST['profile']);    

        if(isset($_REQUEST['profile']['password']) && isset($_REQUEST['profile']['confirm_password'])) {
            if($_REQUEST['profile']['confirm_password'] != $_REQUEST['profile']['password']) {
                return redirect('/profile/view/?msg=passwderr');
            } elseif($_REQUEST['profile']['confirm_password'] && $_REQUEST['profile']['password']) {
                if(($_REQUEST['profile']['confirm_password'] == $_REQUEST['profile']['password'])) {
                    $this->user->password = \Hash::make($_REQUEST['profile']['password']);
                }
            }
        }

        if(isset($_REQUEST['profile']['verify_code']) && $_REQUEST['profile']['verify_code']) {
            $code = $_REQUEST['profile']['verify_code'];
            if(in_array($code, $this->code_list)) {
                $this->user->status = 'active';
                $redirect_url = '/dashboard?msg=account_confirmed';
            } else {
                $redirect_url = '/profile/view?msg=unknown_error';
            }
        }
        
        $this->user->save();
        return redirect($redirect_url);
    }

    public function send_code() 
    {  
        //if(env('APP_ENV') == 'production') {
            $code = $this->code_list[rand(0, count($this->code_list) - 1)];    
            $carrier = Provider::get_carrier_by_id($this->user->sms_provider_id);
            $data = array( 
                'message' => " {$code}",
                'sms' => str_replace("10digitphonenumber", $this->user->sms_number, $carrier->email),
                'email' => $this->user->email
            );
            
            \Mail::raw($data['message'], function ($message) use ($data) {
                $message->from('readydining@gmail.com', '');
                $message->subject('Verify Code');
                $message->to($data['sms'])->cc($data['email']);
            });
        //}

        return redirect('/profile/view/?msg=code_sent');
    }

    public function user_list() {
        $users = User::get_users();
        return view('userlist', ['users' => $users]);
    }

    public function user_loginas() {
        $account = User::find($this->request->input('id'));   
        \Auth::loginUsingId($account->id);
        return redirect("/dashboard");     
    }
}
<?php

namespace App\Http\Controllers;

use App\Tack;
use App\Usermodel;
use App\Ye;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    //
    public function welcome(){
        return view('welcome');
    }
    public function index(){
//        查询业务员循环展示到下拉框
        $data=DB::table('ye')->get();

        return view('index',['data'=>$data]);
    }
//    用户登录方法
    public function login(Request $request){
        $credentials = $request->only('username', 'password');
       $user=Usermodel::where('username',$credentials['username'])
           ->where('password',$credentials['password'])->first();
       if($user){
           session(['user_id'=>$user['id']]);
           return "<script>alert('登录成功')</script>".redirect(url('index'));
       }else{
           return "<script>alert('登录失败')</script>".redirect(url('welcome'));
       }

    }
//    给业务员分配任务方法
    public function save(Request $request){
        $data=$request->validate([
           'tackname'=>'required',
           'name'=>'required',
           'text'=>'required'
        ]);
        $data=Tack::create($data);
       if($data){
           return "添加任务成功";
       }
    }
    public function yelogin(){
        return view('ye');
    }
//    业务员登录
   public function sale(Request $request){
//        取出用户登录的数据
       $credentials = $request->only('name', 'password');
//       如果查出来数据
       $user=Ye::where('name',$credentials['name'])
           ->where('password',$credentials['password'])
           ->first();
       if($user){
//           那就登录成功,存入session
           session(['id'=>$user['id']]);
           return redirect(url('sales'));
       }else{
           return "<script>alert('登录失败')</script>".redirect(url('yelogin'));
       }
   }
//   展示业务员任务视图
   public function sales(){
        $id=session('id');
//        查询该业务员的任务数量
       $count=Tack::where('name',$id)->count();
       return view('sales',['count'=>$count]);
   }
   public function sel(){
//        展示该业务员下的所有任务
       $id=session('id');
       $sale=Tack::where('name',$id)->paginate(2);
       return view('sel',['sale'=>$sale]);
   }
   public function search(Request $request){
        $search=$request->get('search');
        $sale=Tack::where('tackname',$search)
            ->orwhere('created_at',$search)
            ->orwhere('updated_at',$search)
            ->paginate(3);
       return view('sel',['sale'=>$sale]);
   }
}

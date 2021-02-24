<?php
declare (strict_types = 1);

namespace app\middleware;

class Check
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //获取当前参数
        $params = $request->param();
        //获取访问控制器和方法
        $method =$request->rule()->getName();
        //通过字符串分割，获取到具体的类文件和操作的方法名称
        $controller = substr($method,0,strpos($method,'@'));
        $scene = substr($method,strpos($method,'@')+1);
        //拼接验证类名，注意路径不要出错
        $validate = 'app\api\validate\\' . $controller.'Validate';
        //判断当前验证类是否存在
        if(class_exists($validate)){
            $v = new $validate;

            //仅当存在验证场景才校验
            if ($v->hasScene($scene)) {
                //设置当前验证场景
                $v->scene($scene);
                if (!$v->check($params)) {


                    //校验不通过则直接返回错误信息
                    return return_msg('-1',$v->getError());
                }
            }
        }
        return $next($request);
    }
}

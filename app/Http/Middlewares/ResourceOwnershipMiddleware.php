<?php
namespace App\Http\Middlewares;
use App\Http\Middleware;
use App\Core\Authorization\Ownership;
class ResourceOwnershipMiddleware extends Middleware
{
    public function __construct(private string $model,private string $ownerKey='user_id',private string $routeParameter='id'){}
    public function handle($request,$next)
    {
        $id=null;
        if(is_object($request)&&method_exists($request,'route')) $id=$request->route($this->routeParameter);
        if($id===null&&isset($_GET[$this->routeParameter])) $id=$_GET[$this->routeParameter];
        if($id===null) throw new \RuntimeException('Resource identifier missing.');
        $resource=Ownership::resolveAndAuthorize($this->model,$id,null,$this->ownerKey);
        if(is_object($request)) $request->ownedResource=$resource;
        return $next($request);
    }
}

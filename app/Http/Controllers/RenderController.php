<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class RenderController extends Controller
{
    /**
     * universal render method
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    
    public function __invoke(Request $request)
    {
        $routeName = $request->route()->getName();
        $routeName = str_replace(".", "/", $routeName);
        return Inertia::render($routeName);
    }
}
?>
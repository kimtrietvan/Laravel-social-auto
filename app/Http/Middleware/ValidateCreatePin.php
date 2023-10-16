<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ValidateCreatePin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $validate = Validator::make($request->all(), [
            "sess" => "required",
            "board_id" => "required"
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors());
        }
        return $next($request);
    }
}

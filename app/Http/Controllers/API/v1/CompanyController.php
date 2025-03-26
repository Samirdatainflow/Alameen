<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Tbl_User_Master;
use Hash;

class CompanyController extends Controller
{
    public function user_details(Request $request) {
        // try {

        //     if (! $user = JWTAuth::parseToken()->authenticate()) {
        //     return response()->json(['user_not_found'], 404);
        //     }

        //     } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

        //     return response()->json(['token_expired'], $e->getStatusCode());

        //     } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

        //     return response()->json(['token_invalid'], $e->getStatusCode());

        //     } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

        //     return response()->json(['token_absent'], $e->getStatusCode());

        // }

        // return response()->json(compact('user'));
        $data = "Only authorized users can see this";
        return response()->json(compact('data'),200);
    }
}

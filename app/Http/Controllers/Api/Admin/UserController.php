<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function index() {
        return response()->json( [
            'data' => User::with( 'roles' )->latest()->paginate( 20 )
        ] );
    }

    public function show( User $user ) {
        return response()->json( $user->load( 'roles', 'vendor' ) );
    }

    public function destroy( User $user ) {
        $user->delete();
        return response()->json( [ 'message' => 'User deleted' ] );
    }

    public function store( Request $request ) {
        return response()->json( [ 'message' => 'Not implemented' ], 501 );
    }

    public function update( Request $request, User $user ) {
        return response()->json( [ 'message' => 'Not implemented' ], 501 );
    }
}

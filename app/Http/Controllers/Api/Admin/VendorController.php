<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller {
    public function index() {
        $vendors = Vendor::with( 'user' )->latest()->paginate( 20 );
        return response()->json( [ 'data' => $vendors ] );
    }

    public function show( Vendor $vendor ) {
        return response()->json( $vendor->load( 'user', 'products' ) );
    }

    public function approve( Vendor $vendor ) {
        $vendor->update( [ 'status' => 'approved' ] );
        return response()->json( [ 'message' => 'Vendor approved', 'status' => 'approved' ] );
    }

    public function suspend( Vendor $vendor ) {
        $vendor->update( [ 'status' => 'suspended' ] );
        return response()->json( [ 'message' => 'Vendor suspended', 'status' => 'suspended' ] );
    }

    public function destroy( Vendor $vendor ) {
        $vendor->delete();
        return response()->json( [ 'message' => 'Vendor deleted' ] );
    }

    public function store( Request $request ) {
        return response()->json( [ 'message' => 'Not implemented' ], 501 );
    }

    public function update( Request $request, Vendor $vendor ) {
        return response()->json( [ 'message' => 'Not implemented' ], 501 );
    }
}

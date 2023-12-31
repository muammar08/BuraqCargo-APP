<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kurir;
use App\Models\Suplier;
use App\Models\Admin;
use App\Models\BarangSatuan;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{

    protected $user;

    public function __construct() {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminDash()
    {
        $barang = Barang::with(['suplier:id_suplier,nama_suplier'])
            ->whereNull('id_kurir')
            ->get()->toArray();

        return response()->json([
            'success' => true,
            'data' => $barang
        ], 200);
    }

    public function adminDashSatuan() {

        $satuan = BarangSatuan::whereNull('id_kurir')->get()->toArray();

        return response()->json([
            'success' =>true,
            'data' => $satuan
        ], 200);
    }

    public function pickKurir(Barang $barang) {

        $barang = Barang::whereNull('id_kurir')->with(['suplier:id_suplier,nama_suplier'])->get();

        if (!$barang) {
            return response()->json([
                'message' => 'Barang not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'succes' => true,
            'data' => $barang
        ], 200);
    }

    public function pickKurirSatuan(BarangSatuan $barangSatuans) {

        $barangSatuans = BarangSatuan::whereNull('id_kurir')->get();

        if (!$barangSatuans) {
            return response()->json([
                'message' => 'Barang not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'succes' => true,
            'data' => $barangSatuans
        ], 200);
    }

    public function barangDone() {
        $barang = Barang::where('status', 'berhasil')
            ->with(['suplier:id_suplier,nama_suplier', 'kurir:id_kurir,nama_kurir'])
            ->get();

        if ($barang->isEmpty()) {
            return response()->json([
                'message' => 'Barang not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $barang
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [
            'id_suplier'      => $request->namaPerusahaan,
            'no_resi'         => $request->noResi,
            'nama_barang'     => $request->namaBarang,
            'jumlah_barang'   => $request->jumlahBarang,
            'nama_penerima'   => $request->namaPenerima,
            'alamat_penerima' => $request->alamatPenerima,
            'nohp_penerima'   => $request->nohpPenerima,
            'daerah_barang'   => $request->daerahBarang
        ];

        $validator = Validator::make($data, [
            'id_suplier'      => 'required|integer',
            'no_resi'         => 'required|integer',
            'nama_barang'     => 'required|string',
            'jumlah_barang'   => 'required|string',
            'nama_penerima'   => 'required|string',
            'alamat_penerima' => 'required|string',
            'daerah_barang'   => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $existingResi = Barang::where('no_resi', $request->noResi)->first();

        if ($existingResi) {
            return response()->json([
                'success' => false,
                'resiExists' => true,
                'message' => 'Nomor Resi Sudah Ada'
            ], 400);
        } else {

            $barang = Barang::create($data);

            return response()->json([
                'success'  => true,
                'message'  => 'Barang Created',
                'data'     => $barang
            ], 200);
        }
    }

    public function storeSatuan(Request $request) {

        $data = [
            'no_resi_satuan'  => $request->noResiSatuan,
            'nama_barang'     => $request->namaBarang,
            'jumlah_barang'   => $request->jumlahBarang,
            'nama_penerima'   => $request->namaPenerima,
            'alamat_penerima' => $request->alamatPenerima,
            'nohp_penerima'   => $request->nohpPenerima,
            'daerah_satuan'   => $request->daerahSatuan,
            'pembayaran'      => $request->pembayaran,
            'harga'           => $request->harga
        ];

        $validator = Validator::make($data, [
            'no_resi_satuan'  => 'required|integer',
            'nama_barang'     => 'required|string',
            'jumlah_barang'   => 'required|string',
            'nama_penerima'   => 'required|string',
            'alamat_penerima' => 'required|string',
            'pembayaran'      => 'required|string',
            'daerah_satuan'   => 'required|string'
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $existingResi = BarangSatuan::where('no_resi_satuan', $request->noResiSatuan)->first();

        if ($existingResi) {
            return response()->json([
                'success' => false,
                'resiExists' => true,
                'message' => 'Nomor Resi Sudah Ada'
            ], 400);
        } else {
            $satuan = BarangSatuan::create($data);

            return response()->json([
                'success'  => true,
                'message'  => 'Barang Created',
                'data'     => $satuan
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function show(Barang $barang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Barang $barang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Barang $barang)
    {
        //
    }
}
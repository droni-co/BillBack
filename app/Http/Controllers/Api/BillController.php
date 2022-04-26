<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use Auth;

class BillController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $bills = Bill::where('user_id', Auth::user()->id)->orderby('date')->get();
    return response()->json($bills);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'number' => 'required|integer',
      'date' => 'required|date',
      'from_name' => 'required|string',
      'from_document' => 'required|string',
      'to_name' => 'required|string',
      'to_document' => 'required|string',
      'items' => 'required|array',
    ]);
    /* Calculate total */
    $subtotal = 0;
    foreach($request->items as $item) {
      $subtotal += $item['price'] * $item['quantity'];
    }
    $tax = $subtotal * 0.19;
    $total = $subtotal + $tax;

    $bill = new Bill;
    $bill->user_id = Auth::user()->id;
    $bill->number = $request->number;
    $bill->date = $request->date;
    $bill->from_name = $request->from_name;
    $bill->from_document = $request->from_document;
    $bill->to_name = $request->to_name;
    $bill->to_document = $request->to_document;
    $bill->subtotal = $subtotal;
    $bill->tax = $tax;
    $bill->total = $total;
    $bill->items = json_encode($request->items);
    $bill->save();

    return response()->json($bill);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $bill = Bill::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
    return response()->json($bill);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $validated = $request->validate([
      'number' => 'required|integer',
      'date' => 'required|date',
      'from_name' => 'required|string',
      'from_document' => 'required|string',
      'to_name' => 'required|string',
      'to_document' => 'required|string',
      'items' => 'required|array',
    ]);
    
    /* Calculate total */
    $subtotal = 0;
    foreach($request->items as $item) {
      $subtotal += $item['price'] * $item['quantity'];
    }
    $tax = $subtotal * 0.19;
    $total = $subtotal + $tax;

    $bill = Bill::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
    $bill->number = $request->number;
    $bill->date = $request->date;
    $bill->from_name = $request->from_name;
    $bill->from_document = $request->from_document;
    $bill->to_name = $request->to_name;
    $bill->to_document = $request->to_document;
    $bill->subtotal = $subtotal;
    $bill->tax = $tax;
    $bill->total = $total;
    $bill->items = json_encode($request->items);
    $bill->save();

    return response()->json($bill);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $bill = Bill::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
    $bill->delete();
    return response()->json($bill);
  }
}

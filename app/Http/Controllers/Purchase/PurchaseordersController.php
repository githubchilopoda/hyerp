<?php

namespace App\Http\Controllers\Purchase;

 use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Purchase\Purchaseorder;
use App\Models\Purchase\Purchaseorder_hxold;
use App\Models\Purchase\Purchaseorder_hxold_simple;
use App\Http\Requests\Purchase\PurchaseorderRequest;
//use Request;
use App\Models\Purchase\Poitem;
use App\Models\Purchase\Poitem_hxold;
use Carbon\Carbon;
use App\Inventory\Recvitem;


class PurchaseordersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $purchaseorders = Purchaseorder::latest('created_at')->paginate(10);
        return view('purchase.purchaseorders.index', compact('purchaseorders'));
    }

    public function index_hx()
    {
        //
        $purchaseorders = Purchaseorder_hxold::orderBy('id', 'desc')->paginate(10);
        return view('purchase.purchaseorders.index_hx', compact('purchaseorders'));
    }

    public function search_hx(Request $request)
    {
        //
//        dd($request);
        $key = $request->input('key');
        if (strlen($key) > 0)
        {
            $purchaseorders = Purchaseorder_hxold::orderBy('id', 'desc')->where('number', 'like', '%' . $key . '%')->paginate(10);
        }
        else
            $purchaseorders = Purchaseorder_hxold::orderBy('id', 'desc')->paginate(10);
        return view('purchase.purchaseorders.index_hx', compact('purchaseorders'));
    }

    /**
     * Display a listing of the resource by searching order key.
     *
     * @return Response
     */
    public function getitemsbyorderkey($key, $supplierid=0)
    {
        //
        $purchaseorders = Purchaseorder_hxold::where('vendinfo_id', $supplierid)
            ->where(function ($query) use ($key) {
                $query->where('number', 'like', '%'.$key.'%')
                    ->orWhere('descrip', 'like', '%'.$key.'%');
            })
            ->paginate(20); 
        return $purchaseorders;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('purchase.purchaseorders.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(PurchaseorderRequest $request)
    {
        //
        $input = Request::all();
        Purchaseorder::create($input);
        return redirect('purchase/purchaseorders');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
        $purchaseorder = Purchaseorder::findOrFail($id);
        return view('purchase.purchaseorders.edit', compact('purchaseorder'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(PurchaseorderRequest $request, $id)
    {
        //
        $purchaseorder = Purchaseorder::findOrFail($id);
        $purchaseorder->update($request->all());
        return redirect('purchase/purchaseorders');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
        Purchaseorder::destroy($id);
        return redirect('purchase/purchaseorders');
    }
    
    public function detail($id)
    {
        $poitems = Poitem::latest('created_at')->where('pohead_id', $id)->paginate(10);
        return view('purchase.poitems.index', compact('poitems', 'id'));
    }

    public function detail_hxold($id)
    {
        $poitems = Poitem_hxold::where('pohead_id', $id)->orderBy('id')->paginate(10);
        return view('purchase.poitems_hxold.index', compact('poitems', 'id'));
    }
    
    public function receiving($id)
    {
        $poitems = Purchaseorder::find($id)->poitems;
        foreach ($poitems as $poitem)
        {
            $forQtyReceive = $poitem->qty_ordered - $poitem->qty_received;
            if ($forQtyReceive > 0.0)
            {
                $itemsite = $poitem->itemsite;
                if ($itemsite == null)
                    return $poitem->item->item_number . '无库存记录';
        
//                 if ($itemsite->qtyonhand < $forQtyReceive)
//                     return $soitem->item->item_number . ', 库存已不够，无法发货。';
        
                // create receive record
                $data = [
                    'orderitem_id' => $poitem->id,
                    'quantity' => $forQtyReceive,
                    'recvdate' => Carbon::now(),
                ];
                Recvitem::create($data);
        
                // update soitem qtyshipped
                $poitem->qty_received = $poitem->qty_received + $forQtyReceive;
                $poitem->save();
        
                // update itemsite qtyonhand
                $itemsite->qtyonhand = $itemsite->qtyonhand + $forQtyReceive;
                $itemsite->save();
            }
        }
        return redirect('purchase/purchaseorders');
    }

    // 收货单
    public function receiptorders($id)
    {
        $receiptorders = Purchaseorder_hxold::find($id)->receiptorders;
        // dd($receiptorders);
        // foreach ($poitems as $poitem)
        // {
        //     $forQtyReceive = $poitem->qty_ordered - $poitem->qty_received;
        //     if ($forQtyReceive > 0.0)
        //     {
        //         $itemsite = $poitem->itemsite;
        //         if ($itemsite == null)
        //             return $poitem->item->item_number . '无库存记录';
        
        //         // create receive record
        //         $data = [
        //             'orderitem_id' => $poitem->id,
        //             'quantity' => $forQtyReceive,
        //             'recvdate' => Carbon::now(),
        //         ];
        //         Recvitem::create($data);
        
        //         // update soitem qtyshipped
        //         $poitem->qty_received = $poitem->qty_received + $forQtyReceive;
        //         $poitem->save();
        
        //         // update itemsite qtyonhand
        //         $itemsite->qtyonhand = $itemsite->qtyonhand + $forQtyReceive;
        //         $itemsite->save();
        //     }
        // }
        return view('purchase.receiptorders.index', compact('receiptorders'));
    }

    // 收货单
    public function poitems($id)
    {
        $poitems = Purchaseorder_hxold::findOrFail($id)->poitems;
        dd($poitems);

        return view('purchase.receiptorders.index', compact('receiptorders'));
    }

    // 入库单
    public function receiptorders_hx($id)
    {
        $receiptorders = Purchaseorder_hxold::find($id)->receiptorders;
//        dd($receiptorders);
        return view('purchase.receiptorders.index_hx', compact('receiptorders'));
    }
}

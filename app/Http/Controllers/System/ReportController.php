<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\System\Report;
use App\Models\Purchase\Purchaseorder_hxold;
use App\Http\Controllers\HelperController;
use DB, Excel;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $reports = Report::latest('created_at')->paginate(10);
        return view('system.report.index', compact('reports'));
    }

    public function indexpurchase()
    {
        $reports = Report::latest('created_at')->where('module', '采购')->where('active', 1)->paginate(10);
        $readonly = true;
        return view('system.report.index', compact('reports', 'readonly'));
    }

    public function indexsales()
    {
        $reports = Report::latest('created_at')->where('module', '销售')->where('active', 1)->paginate(10);
        $readonly = true;
        return view('system.report.index', compact('reports', 'readonly'));
    }

    public function indexinventory()
    {
        $reports = Report::latest('created_at')->where('module', '库存')->where('active', 1)->paginate(10);
        $readonly = true;
        return view('system.report.index', compact('reports', 'readonly'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('system.report.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        Report::create($input);
        return redirect('system/report');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $report = Report::findOrFail($id);
//        var_dump($report->active);
        return view('system.report.edit', compact('report'));
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
        //
//        dd($request->all());
        $report = Report::findOrFail($id);
        $report->update($request->all());
        return redirect('system/report');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Report::destroy($id);
        return redirect('system/report');
    }

    public function statistics($id)
    {
        //
        $report = Report::findOrFail($id);

        $request = Request();

        $input = $request->all();
        $input = HelperController::skipEmptyValue($input);
        $input = array_except($input, '_token');
        $input = array_except($input, 'page');
//
//        $param = "";
//        foreach ($input as $key=>$value)
//        {
//            $param .= "@" . $key . "='" . $value . "',";
//        }
//        $param = count($input) > 0 ? substr($param, 0, strlen($param) - 1) : $param;
//        $items_t = DB::connection('sqlsrv')->select($report->statement . ' ' . $param);
        $items_t = $this->search(Request(), $report);
//        dd($items_t);
//        dd(json_decode(json_encode($items_t), true));

        $page = $request->get('page', 1);
        $paginate = 15;
        $offSet = ($page * $paginate) - $paginate;
        $itemsForCurrentPage = array_slice($items_t, $offSet, $paginate, true);
        $items = new \Illuminate\Pagination\LengthAwarePaginator($itemsForCurrentPage, count($items_t), $paginate, $page);
//        dd($items);
//        dd(array_first(array_first($items->items())));

        return view('system.report.statisticsindex', compact('items', 'report', 'input'));
    }

    public function export($id)
    {
        //
        $report = Report::findOrFail($id);

        Excel::create($report->name, function($excel) use ($report) {
            $excel->sheet('Sheetname', function($sheet) use ($report) {

                // Sheet manipulation
//                $request = Request();
//                $input = $request->all();
//                $input = HelperController::skipEmptyValue($input);
//                $input = array_except($input, '_token');
//                $input = array_except($input, 'page');
//
//                $param = "";
//                foreach ($input as $key=>$value)
//                {
//                    $param .= "@" . $key . "='" . $value . "',";
//                }
//                $param = count($input) > 0 ? substr($param, 0, strlen($param) - 1) : $param;
//                $items_t = DB::connection('sqlsrv')->select($report->statement . $param);

                $items_t = $this->search(Request(), $report);

//                $items_t = DB::connection('sqlsrv')->select("select * from vpurchaseorder");
//                dd($items_t);
//                dd(json_decode(json_encode($items_t), true));
//                $paymentrequests = $this->search2()->toArray();
                $sheet->freezeFirstRow();
//                $sheet->setColumnFormat(array(
//                    'C' => 'yyyy-mm-dd',
//                    'G' => '0.00%'
//                ));
//                $sheet->fromArray(json_decode(json_encode($items_t), true));
                $dataArray = json_decode(json_encode($items_t), true);
                list($keys, $values) = array_divide(array_first($dataArray));
                $sheet->appendRow($keys);
                foreach ($dataArray as $value)
                    $sheet->appendRow($value);

//                $sheet->fromModel($items_t);
//                $sheet->protect('password');
//                $sheet->setColumnFormat(array('G' => '0%'));
//                $sheet->row(1, function ($row) {
//                    $row->setBackground('#000000');
//                });
//                $sheet->appendRow(array(
//                    'appended', 54546, '2017-05-01', 1, 1, 1, '0.5'
//                ));
            });

            // Set the title
            $excel->setTitle($report->name);

            // Chain the setters
            $excel->setCreator('HXERP')
                ->setCompany('Huaxing East');

            // Call them separately
            $excel->setDescription($report->descrip);

        })->export('xlsx');

    }

    private function search($request, $report)
    {
        $input = $request->all();
        $input = HelperController::skipEmptyValue($input);
        $input = array_except($input, '_token');
        $input = array_except($input, 'page');

        $param = "";
        foreach ($input as $key=>$value)
        {
            $param .= "@" . $key . "='" . $value . "',";
        }
        $param = count($input) > 0 ? substr($param, 0, strlen($param) - 1) : $param;
        $items_t = DB::connection('sqlsrv')->select($report->statement . ' ' . $param);

        return $items_t;
    }

}

<?php

namespace App\Services;


use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Transaction;

class TransactionService
{

    protected array $transactionFilter = [
        'transaction_no',
        'payment_method',
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return Transaction::with('order')->with(['order' => function ($query) {
                $query->select('id', 'order_serial_no');
            }])->where(function ($query) use ($requests) {
                if (isset($requests['order_serial_no'])) {
                    $query->whereHas('order', function ($query) use ($requests) {
                        $query->where(['order_serial_no' => $requests['order_serial_no']]);
                    });
                }

                if (isset($requests['from_date']) && isset($requests['to_date'])) {
                    $first_date = date('Y-m-d', strtotime($requests['from_date']));
                    $last_date  = date('Y-m-d', strtotime($requests['to_date']));
                    $query->whereDate('created_at', '>=', $first_date)->whereDate(
                        'created_at',
                        '<=',
                        $last_date
                    );
                }
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->transactionFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}

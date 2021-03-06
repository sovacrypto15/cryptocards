<?php

namespace App\Http\Controllers;

use App\Helpers\EthereumConverter;
use App\Models\Battle;
use App\Models\Card;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;

class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['jwt.auth', 'admin']);
    }

    public function getCounts()
    {
        $counts = [
            'user'    => User::count(),
            'card'    => Card::count(),
            'listing' => Listing::count(),
        ];

        return response()->build(self::RESPONSE_MESSAGE_SUCCESS, $counts);
    }

    public function getTransactionReport()
    {
        $avg_price = EthereumConverter::convertETHPriceToFloat(Transaction::avg('price'));
        $max_price = EthereumConverter::convertETHPriceToFloat(Transaction::max('price'));
        $min_price = EthereumConverter::convertETHPriceToFloat(Transaction::min('price'));
        $volume = EthereumConverter::convertEthPriceToFloat(Transaction::sum('price'));

        $transactions = Transaction::select(
            \DB::raw('DATE(`created_at`) AS day'),
            \DB::raw('SUM(price) AS price_sum')
        )->groupBy('day')
          ->get();

        $cards = Card::select(
            \DB::raw('DATE(`created_at`) AS day'),
            \DB::raw('COUNT(*) AS num_cards')
        )->groupBy('day')
          ->get();

        $report = [
            'avg_price'    => $avg_price,
            'max_price'    => $max_price,
            'min_price'    => $min_price,
            'transactions' => $transactions,
            'volume'       => $volume,
            'cards'        => $cards,
        ];

        return response()->build(self::RESPONSE_MESSAGE_SUCCESS, $report);
    }

    public function getEntranceFeeReport()
    {
        $avg_fee = EthereumConverter::convertETHPriceToFloat(Battle::avg('entrance_fee'));
        $max_fee = EthereumConverter::convertETHPriceToFloat(Battle::max('entrance_fee'));
        $min_fee = EthereumConverter::convertETHPriceToFloat(Battle::min('entrance_fee'));
        $volume = EthereumConverter::convertEthPriceToFloat(Battle::sum('entrance_fee'));

        $entrance_fees = Battle::select(
            \DB::raw('DATE(`created_at`) AS day'),
            \DB::raw('SUM(`entrance_fee`) AS fee_sum')
        )->groupBy('day')
          ->get();

        $report = [
            'avg_entrance_fee'    => $avg_fee,
            'max_entrance_fee'    => $max_fee,
            'min_entrance_fee'    => $min_fee,
            'entrance_fees'       => $entrance_fees,
            'volume'              => $volume,
        ];

        return response()->build(self::RESPONSE_MESSAGE_SUCCESS, $report);
    }
}

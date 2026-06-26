<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Hiển thị giao diện Ví của tôi.
     */
    public function index()
    {
        $user = auth()->user();

        // Lịch sử chi tiêu: Các công thức đã mua
        $purchases = $user->purchasedRecipes()->with('user')->orderBy('recipe_user_purchases.created_at', 'desc')->get();

        // Lịch sử thu nhập: Các công thức của bản thân được người khác mua
        $earnings = DB::table('recipe_user_purchases')
            ->join('recipes', 'recipe_user_purchases.recipe_id', '=', 'recipes.id')
            ->join('users', 'recipe_user_purchases.user_id', '=', 'users.id')
            ->where('recipes.user_id', $user->id)
            ->select(
                'recipe_user_purchases.created_at',
                'recipe_user_purchases.price',
                'users.name as buyer_name',
                'recipes.title as recipe_title',
                'recipes.slug as recipe_slug'
            )
            ->orderBy('recipe_user_purchases.created_at', 'desc')
            ->get();

        return view('wallet.index', compact('user', 'purchases', 'earnings'));
    }

    /**
     * Nạp tiền ảo để kiểm tra tính năng.
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000|max:10000000'
        ], [
            'amount.required' => 'Vui lòng chọn hoặc nhập số tiền.',
            'amount.numeric' => 'Số tiền không hợp lệ.',
            'amount.min' => 'Số tiền nạp tối thiểu là 10.000 đ.',
            'amount.max' => 'Số tiền nạp tối đa một lần là 10.000.000 đ.'
        ]);

        $amount = $request->input('amount');
        $user = auth()->user();
        $user->increment('balance', $amount);

        return redirect()->back()->with('success', 'Nạp tiền vào ví thành công! +' . number_format($amount, 0, ',', '.') . ' đ');
    }

    /**
     * Đăng ký gói Premium bằng số dư ví.
     */
    public function buyPremium(Request $request)
    {
        $user = auth()->user();

        // 1. Kiểm tra xem đã là Premium chưa
        if ($user->is_premium) {
            return redirect()->back()->with('error', 'Bạn đã là thành viên Premium rồi!');
        }

        // 2. Phí đăng ký Premium là 100.000 VND
        $price = 100000;
        if ($user->balance < $price) {
            return redirect()->route('wallet.index')->with('error', 'Số dư tài khoản không đủ để đăng ký Premium! Vui lòng nạp thêm tiền.');
        }

        try {
            DB::beginTransaction();

            // Trừ tiền người dùng
            $user->decrement('balance', $price);

            // Cập nhật Premium status
            $user->is_premium = true;
            $user->save();

            DB::commit();

            return redirect()->route('home')->with('success', 'Chúc mừng! Bạn đã đăng ký thành công gói Premium (100.000 đ / tháng) 👑');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đăng ký gói Premium thất bại: ' . $e->getMessage());
        }
    }
}

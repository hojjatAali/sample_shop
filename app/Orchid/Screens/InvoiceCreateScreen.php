<?php

namespace App\Orchid\Screens;

use App\Models\Cart;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Extension\Table\TableRow;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use PhpParser\Node\Stmt\Echo_;

class InvoiceCreateScreen extends Screen
{
    public $products;
    public $cart;
    public $total_cost;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $user = Auth::user();
        $this->products = Product::all();
        if ($user->cart) {
            $this->cart = $user->cart->products;
            $this->total_cost = DB::table('cart_product')->where('cart_id', $user->cart->id)->sum('price_of_product');
        } else {
            $this->cart = new Cart();
            $this->cart->user_id = $user->id;
            $this->cart->save();
        }
        return [
            'products' => $this->products,
            'cart' => $this->cart,
            'total_cost' => $this->total_cost,
        ];
    }


    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'InvoiceCreateScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::accordion([
                "General Information" => [
                    Layout::rows([
                        Input::make('invoice.seller_name')
                            ->title('Seller Name')
                        , Input::make('invoice.customer_name')
                            ->title('Costumer Name')
                        , Input::make('invoice.driver_name')
                            ->title('Driver Name'),
                        Input::make('invoice.description')
                            ->title('Description'),
                        Input::make('invoice.address')
                            ->title('Address')
                        , Input::make('invoice.recipient')
                            ->title('Recipient')
                        , Input::make('invoice.discount')
                            ->title('Discount')
                            ->type('number'),
                        Input::make('invoice.shipping_cost')
                            ->title('Shipping_cost')
                        , Input::make('invoice.pay_deadline')
                            ->title('Pay_deadline')
                            ->type('date')
                    ])
                ],
                "Payment" => [
                    Layout::rows([
                        RadioButtons::make('invoice.status')
                            ->title('Status')
                            ->options([
                                "in_progress" => "in_progress",
                                "delivered" => "delivered",
                                "canceled" => "canceled",
                                "confirmation" => "confirmation",
                            ]),
                    ])
                ]
            ]),
            Layout::rows([Select::make('product.id')
                ->fromQuery(Product::query(), 'display_name')
                ->searchColumns('product_name',)
                ->title('Product'),
                Input::make('product.quantity')
                    ->title('Quantity')
                    ->type('number')
                    ->value(1),
                Button::make('submit')
                    ->type(Color::BASIC())
                    ->method('addToCart')
            ]),
            Layout::table('cart', [
                TD::make('product_name')->sort(),
                TD::make('selling_price'),
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn(Product $product) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Button::make('delete')
                                ->confirm('are you want to delete product ???')
                                ->icon('trash')
                                ->method('removeFromCart', ['product' => $product->id]),
                            Button::make('-')
                                ->method('decrementProduct', ['product' => $product->id]),
                            Button::make('+')
                                ->method('incrementProduct', ['product' => $product->id])
                        ])),
            ]),
            Layout::rows([
                Button::make('Click for send to incoices ' . " " . "Total Price " . $this->total_cost)
                    ->type(Color::INFO())
                    ->method('createInvoice')
            ])
        ];
    }

    public function addToCart(Request $request)
    {
        $user = Auth::user();
        $product_id = $request->input('product.id');
        $product = Product::find($product_id);
        $quantity = $request->input('product.quantity');
        $price_of_product = $product->selling_price * $quantity;
        if ($user->cart->products()->where('product_id', $product_id)->exists()) {
            $user->cart->products()->updateExistingPivot($product_id, ['quantity' => DB::raw('quantity + ' . $quantity)]);
            $user->cart->products()->updateExistingPivot($product_id, ['price_of_product' => DB::raw('price_of_product +' . $price_of_product)]);
        } else {
            $user->cart->products()->attach($product_id, ['quantity' => $quantity, 'price_of_product' => $price_of_product]);

        }
    }

    public function removeFromCart(Product $product)
    {
        $user = Auth::user();
        $user->cart->products()->detach($product->id);
    }

    public function incrementProduct(Product $product)
    {
        $user = Auth::user();
        $price_of_product = $product->selling_price;
        $user->cart->products()->updateExistingPivot($product, ['quantity' => DB::raw('quantity + 1'), 'price_of_product' => DB::raw('price_of_product +' . $price_of_product)]);
    }

    public function decrementProduct(Product $product)
    {
        $user = Auth::user();
        $quantity = $user->cart->products()->where('product_id', $product->id)->first()->pivot->quantity;
        $price_of_product = $product->selling_price;
        if ($quantity == 1) {
            $user->cart->products()->detach($product->id);
        } else {
            $user->cart->products()->updateExistingPivot($product, ['quantity' => DB::raw('quantity - 1 '), 'price_of_product' => DB::raw('price_of_product -' . $price_of_product)]);
        }
    }

    public function createInvoice(Request $request)
    {
        if (!Auth::user()->cart->product) {
            Alert::message('no products for invoice ');
            return redirect()->route('invoice.create');
        }
        $cart = Auth::user()->cart;
        $total_price = DB::table('cart_product')->where('cart_id', $cart->id)->sum('price_of_product') + $request->input('invoice.shipping_cost');
        $invoice = new Invoice();
        $invoice->user_id = $cart->user_id;
        $invoice->seller_name = $request->input('invoice.seller_name');
        $invoice->customer_name = $request->input('invoice.customer_name');
        $invoice->driver_name = $request->input('invoice.driver_name');
        $invoice->discount = $request->input('invoice.discount');
        $invoice->total_price = $total_price;
        $invoice->price_after_discount = $total_price - $request->input('invoice.discount');
        $invoice->shipping_cost = $request->input('invoice.shipping_cost');
        $invoice->description = $request->input('invoice.description');
        $invoice->address = $request->input('invoice.address');
        $invoice->recipient = $request->input('invoice.recipient');
        $invoice->pay_deadline = $request->input('invoice.pay_deadline');
        $invoice->save();

        foreach ($cart->products as $product) {
            $invoice->products()->attach($product->id, ['quantity' => $product->pivot->quantity, 'price_of_product' => $product->pivot->price_of_product]);
        }
        $cart->delete();
        $cart = new Cart();
        $cart->user_id = Auth::user()->id;
        $cart->save();
        Alert::message('Success');
        return redirect()->route('invoice.create');
    }

}

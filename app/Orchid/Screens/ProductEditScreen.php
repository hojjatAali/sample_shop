<?php

namespace App\Orchid\Screens;

use App\Models\Product;

//use http\Env\Request;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Laravel\Prompts\Table;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\RadioButtons;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

use function Symfony\Component\Translation\t;

class ProductEditScreen extends Screen
{
    /**
     * @var Product
     */
    public $product;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */

    public function query($product_id): array
    {
        return [
            $this->product = Product::find($product_id)
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->product->product_name;
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
     * @return Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('product.product_name')
                    ->title('Product Name')
                    ->value($this->product->product_name)
                    ->type('text'),
                Input::make('product.barcode')
                    ->title('Barcode')
                    ->value($this->product->barcode)
                    ->type('number'),
                CheckBox::make('product.is_active')
                    ->title('Active?')
                    ->value($this->product->is_active),
                Input::make('product.expire_date')
                    ->title('EX date')
                    ->value($this->product->expire_date)
                    ->type('date'),
                Input::make('product.produce_date')
                    ->title('P date')
                    ->value($this->product->produce_date)
                    ->type('date'),
                Input::make('product.max_in_card')
                    ->title('Max in card')
                    ->value($this->product->max_in_card)
                    ->type('number'),
                Input::make('product.purchase_price')
                    ->title('Purchase Price')
                    ->value($this->product->purchase_price)
                    ->type('number'),
                Input::make('product.selling_price')
                    ->title('Selling Price')
                    ->value($this->product->selling_price)
                    ->type('number'),
                Input::make('product.customer_price')
                    ->title('Customer Price')
                    ->value($this->product->customer_price)
                    ->type('number'),
                Input::make('product.quantity_in_box')
                    ->title('Quantity In Box')
                    ->value($this->product->quantity_in_box)
                    ->type('number'),
                RadioButtons::make('product.package_type')
                    ->title('Package Type')
                    ->options([
                        "sack" => "sack",
                        "package" => "package",
                        "carton" => "carton",
                        "box" => "box"
                    ])
                    ->value($this->product->package_type),
                Button::make('save')
                    ->method('update', ["product_id" => $this->product->id])

            ])
        ];
    }
    public function update(Request $request, $product_id)
    {
        $product = Product::find($product_id);
        $product->fill($request->get('product'))->save();

        return redirect()->route('products.list');
    }
}

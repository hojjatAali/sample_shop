<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use function Termwind\render;

class ProductScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'products' => Product::get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'ProductScreen';
    }

    public function description(): string
    {
        return "view/add and edit all products";
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add product')
                ->modal('productModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('products', [
                TD::make('product_name')
                    ->sort(),
                TD::make('max_in_card'),
                TD::make('selling_price'),
                TD::make('quantity_in_box'),
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn(Product $product) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('product.edit', $product->id)
                                ->icon('bs.pencil'),
                            Button::make('delete')
                                ->confirm('are you want to delete product ???')
                                ->icon('trash')
                                ->method('delete', ['product' => $product->id])
                        ])),


            ]),
            Layout::modal('productModal', Layout::rows([
                Input::make('product.product_name')
                    ->title('product_name')
                    ->placeholder('Enter product name')
                    ->help('The name of the product to be created.'),
                Input::make('product.barcode')
                    ->title('barcode'),
                Input::make('product.expire_date')
                    ->type('date')
                    ->title('EX date'),
                Input::make('product.produce_date')
                    ->type('date')
                    ->title('P date'),
                Input::make('product.max_in_card')
                    ->title('Max In Card')
                    ->type('number')
                    ->value(100),
                Input::make('product.purchase_price')
                    ->title('purchase_price')
                    ->type('number'),
                Input::make('product.selling_price')
                    ->title('selling_price')
                    ->type('number'),
                Input::make('product.customer_price')
                    ->title('customer_price')
                    ->type('number'),
                Input::make('product.quantity_in_box')
                    ->title('quantity_in_box_price')
                    ->type('number'),
                CheckBox::make('product.is_active')
                    ->title('is_active')
                    ->value(1),
                Select::make('product.package_type')
                    ->title('package type')
                    ->options([
                        "sack" => "sack",
                        "package" => "package",
                        "carton" => "carton",
                        "box" => "box"
                    ])
                    ->empty('carton')
                    ->value('carton')
            ]))
                ->title('Create Product')
                ->applyButton('Add Product'),
        ];
    }

    public function create(Request $request)
    {
        $request->validate([
            "product.product_name" => ["required", "max:30"],
            "product.barcode" => ["required", "max:50"],
            "product.expire_date" => ["nullable"],
            "product.produce_date" => ["nullable"],
            "product.max_in_card" => ["required"],
            "product.purchase_price" => ["required"],
            "product.selling_price" => ["required",
                function ($attribute, $value, $fail) use ($request) {
                    if ($value <= $request->input('product.purchase_price')) {
                        $fail($attribute . ' should be greater than purchase price.');
                    }
                },
            ],
            "product.customer_price" => ["required", "nullable"],
            "product.quantity_in_box" => ["required", "max:200"],
        ]);

        $new_product = Product::create([
            "product_name" => $request->input('product.product_name'),
            "barcode" => $request->input('product.barcode'),
            "expire_date" => $request->input('product.expire_date'),
            "produce_date" => $request->input('product.produce_date'),
            "max_in_card" => $request->input('product.max_in_card'),
            "purchase_price" => $request->input('product.purchase_price'),
            "selling_price" => $request->input('product.selling_price'),
            "customer_price" => $request->input('product.customer_price'),
            "quantity_in_box" => $request->input('product.quantity_in_box'),
            "package_type" => $request->input('product.package_type')
        ]);
        Alert::message('new product created');
        return redirect()->route('products.list');

    }

    public function delete(Product $product)
    {
        $product->delete();
        return [
            Alert::warning('deleted successfully')
        ];
    }


}

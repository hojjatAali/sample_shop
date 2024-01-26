<?php

namespace App\Orchid\Screens;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Validation\Rules\In;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Menu;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class InvoiceScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'invoices' => Invoice::all()
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Invoices';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Menu::make('Add')
                ->route('invoice.create')
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
            Layout::table('invoices', [
                TD::make('customer_name')
                    ->sort(),
                TD::make('seller_name'),
                TD::make('price_after_discount'),
                TD::make(__('Actions'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn(Invoice $invoice) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('invoice.edit', $invoice->id)
                                ->icon('bs.pencil'),
                            Button::make('delete')
                                ->confirm('are you want to delete invoice ???')
                                ->icon('trash')
                                ->method('delete', ['invoice_id' => $invoice->id])
                        ])),

            ])
        ];
    }

    public function delete( $invoice_id)
    {
        $invoice=Invoice::find($invoice_id);
        $invoice->delete();

        return redirect()->route('invoice.list');
    }
}

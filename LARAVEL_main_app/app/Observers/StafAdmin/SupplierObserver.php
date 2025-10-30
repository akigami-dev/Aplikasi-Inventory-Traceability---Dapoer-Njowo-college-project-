<?php

namespace App\Observers\StafAdmin;

use App\Models\Supplier;

class SupplierObserver
{
    /**
     * Handle the Supplier "created" event.
     */
    public function created(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "updated" event.
     */
    public function updated(Supplier $supplier): void
    {
        //
    }

    public function creating(Supplier $supplier): void
    {
        // Get the last created product code
        $lastProduct = Supplier::latest('id')->first();
        
        if ($lastProduct) {
            $lastKode = (int)substr($lastProduct->kode_supplier, 2); // Get the numeric part
            $newKode = $lastKode + 1;
        } else {
            $newKode = 1;
        }
        
        // Format the code with leading zeros (PK001, PK002, etc.)
        $supplier->kode_supplier = 'KS' . str_pad($newKode, 3, '0', STR_PAD_LEFT);
        $supplier->updated_at = now();
        $supplier->created_at = now();
    }

    public function updating(Supplier $supplier): void
    {
        $supplier->updated_at = now();
    }

    /**
     * Handle the Supplier "deleted" event.
     */
    public function deleted(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "restored" event.
     */
    public function restored(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "force deleted" event.
     */
    public function forceDeleted(Supplier $supplier): void
    {
        //
    }
}

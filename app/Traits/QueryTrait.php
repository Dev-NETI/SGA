<?php

namespace App\Traits;

use Exception;

trait QueryTrait
{

    public function storeTrait($query, $errorMsg, $successMsg)
    {
        try {
            if (!$query) {
                session()->flash('error', $errorMsg);
            }
            session()->flash('success', $successMsg);
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function updateTrait($data, $routeBack, $query, $errorMsg, $successMsg)
    {
        if (!$data) {
            session()->flash('error', 'Data not found');
            return $this->redirectRoute($routeBack);
        }
        $this->storeTrait($query, $errorMsg, $successMsg);
    }

    public function updateTraitNoRoute($data, $query, $errorMsg, $successMsg)
    {
        if (!$data) {
            session()->flash('error', 'Data not found');
        }
        $this->storeTrait($query, $errorMsg, $successMsg);
    }

    public function hardDestroyTrait($model, $attribute, $parameter)
    {
        try {
            $destroy = $model::where($attribute, $parameter)->first()->delete();
            if (!$destroy) {
                session()->flash('error', 'Deleted successfully!');
            }
            session()->flash('success', 'Deletion failed!');
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function saveFileToStorage($storagePath, $filename)
    {
        $save = $this->file->storeAs($storagePath, $filename);
        return $save;
    }
}

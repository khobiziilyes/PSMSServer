<?php

namespace App\Http\Controllers\ControllersTraits;

use Illuminate\Http\Request;

trait destroyModel {
	public function destroy($id) {
        if (property_exists($this, 'beforeDestroy')) {
            $theInstance = $this->theClass::findOrFail($id);
            $beforeDestroy = $this->beforeDestroy;
            
            $itemsCount = is_null($beforeDestroy) ? 0 : $theInstance->$beforeDestroy->count();
            $deleted = false;
            
            if ($itemsCount === 0) {
                $theInstance->delete();
                $deleted = true;
            }
            
            return ['deleted' => $deleted];
        }
        
        return abort(404);
    }
}
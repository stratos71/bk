<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Cotizacion;

class RegistrosController extends Controller
{

    public function index()
    {
        
        
        $cotizaciones1 = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'En espera de respuesta')->count();
        $cotizaciones2 = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'En espera de aceptacion')->orWhere('estado', 'En espera de aceptacion+')->count();
        $cotizaciones3 = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'En espera de confirmacion de pago')->count();
        $cotizaciones4 = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'En proceso de pedido')->count();
        $cotizaciones5 = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Piezas en locacion')->count();
        $cotizaciones6 = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Despachado')->count();
        $cotizaciones7 = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Recepcionado en Bolivia')->count();
        $cotizaciones8 = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Finalizado')->count();
        $cotizaciones9 = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Cancelado')->count();
        return response()->json([
            'a'=> $cotizaciones1,
            'b'=> $cotizaciones2,
            'c'=> $cotizaciones3,
            'd'=> $cotizaciones4,
            'e'=> $cotizaciones5,
            'f'=> $cotizaciones6,
            'g'=> $cotizaciones7,
            'h'=> $cotizaciones8,
            'i'=> $cotizaciones9
        ]);
    }



    public function tabla1()
    {
        $user = Auth::user();
        $cotizaciones = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'En espera de respuesta')->latest()->get();
        return response()->json([
            'cotizaciones' => $cotizaciones,
            'user' => $user
        ]);
    }
    public function tabla2()
    {
        $cotizaciones = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'En espera de aceptacion')->orWhere('estado', 'En espera de aceptacion+')->latest()->get();
        
        return response()->json([
            'cotizaciones' => $cotizaciones
        ]);
    }
    public function tabla3()
    {
        $cotizaciones = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'En espera de confirmacion de pago')->latest()->get();
        return response()->json([
            'cotizaciones' => $cotizaciones
        ]);
    }
    public function tabla4()
    {
        $cotizaciones = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'En proceso de pedido')->latest()->get();
        return response()->json([
            'cotizaciones' => $cotizaciones
        ]);
    }
    public function tabla5()
    {
        $cotizaciones = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Piezas en locacion')->latest()->get();
        return response()->json([
            'cotizaciones' => $cotizaciones
        ]);
    }
    public function tabla6()
    {
        $cotizaciones = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Despachado')->latest()->get();
        return response()->json([
            'cotizaciones' => $cotizaciones
        ]);
    }
    public function tabla7()
    {
        $cotizaciones = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Recepcionado en Bolivia')->latest()->get();
        return response()->json([
            'cotizaciones' => $cotizaciones
        ]);
    }
    public function tabla8()
    {
        $cotizaciones = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Finalizado')->latest()->get();
        return response()->json([
            'cotizaciones' => $cotizaciones
        ]);
    }
    public function tabla9()
    {
        $cotizaciones = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('estado', 'Cancelado')->latest()->get();
        return response()->json([
            'cotizaciones' => $cotizaciones
        ]);
    }
}

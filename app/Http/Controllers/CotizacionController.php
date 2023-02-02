<?php

namespace App\Http\Controllers;

use Twilio\Rest\Client;
use App\Mail\AutoboCotizacion;
use App\Mail\AutoboCotizacionUSA;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Cotizacion;
use App\Models\Repuesto;
use App\Models\Imagen;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class CotizacionController extends Controller
{
    public function cotizacion(Request $request)
    {
        $cotizacion = json_decode($request->cotizacion);
        $c = new Cotizacion();
        $c->nombre = $cotizacion->nombre;
        $c->telefono = $cotizacion->telefono;
        $c->email = $cotizacion->email;
        $c->vin = $cotizacion->vin;
        $c->estado = 'En espera de respuesta';
        $c->save();
        $c->codigo = $c->id . '-' . substr($c->telefono, -3);
        $c->save();
        $cotizacionId = $c->id;
        $j = 0;
        foreach ($cotizacion->repuestos as $repuesto) {
            $r = new Repuesto();
            $r->cotizacion_id = $cotizacionId;
            $r->nombre = $repuesto->nombre;
            $r->numero_pieza = $repuesto->numero_pieza;
            $r->cantidad = $repuesto->cantidad;
            $r->save();
            $repuestoId = $r->id;

            $q = 0;
            if ($repuesto->imagenes) {
                foreach ($repuesto->imagenes as $imagen) {
                    $foto = $request->file('repuesto_' . $j . '_imagen_' . $q);
                    $extension = $foto->guessExtension();
                    $filename = time() . '-' . $j . '-' . $q . '.' . $extension;
                    $ruta = $foto->move(public_path('images'), $filename);
                    $i = new Imagen();
                    $i->repuesto_id = $repuestoId;
                    $i->nombre = $filename;
                    $i->ruta = $ruta;
                    $i->save();
                    $q++;
                }
            }
            $j++;
        }

        
        $client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
        $client->messages->create(
            $c->telefono,
            array(
                'from' => env('TWILIO_FROM_NUMBER'),
                'body' => 'AUTOBO: Estimado cliente, responderemos a su consulta en un plazo maximo de 48 horas. Gracias por contactarnos. Su código de orden es '.$c->codigo.'. Para ver el estado de su consulta ingrese a: http://144.22.54.25/invitado/consulta y digite su codigo'
            )
        );
        

      
        $destinatario = env('CORREO_BOLIVIA');
        Mail::to($destinatario)
            ->send(new AutoboCotizacion($c));


        $destinatario = env('CORREO_MIAMI');
        Mail::to($destinatario)
            ->send(new AutoboCotizacionUSA($c));



        return response()->json([
            'success' => true,
            'codigo' => $c->codigo,
        ]);
    }

    public function consulta(Request $request)
    {


        $cotizacion = Cotizacion::with(['repuestos', 'repuestos.imagenes'])
            ->where('codigo', '=', $request->codigo)
            ->first();
            
            

        if ($cotizacion === null) {

            return response()->json([
                'show' => false,
                'consulta' => '',
                'msg' => 'No existen cotizaciones',
            ]);
        } else
            return response()->json([
                'show' => true,
                'consulta' => $cotizacion,
                'msg' => '',
            ]);
    }
}

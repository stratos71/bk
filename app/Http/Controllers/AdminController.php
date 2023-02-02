<?php

namespace App\Http\Controllers;

use Luecano\NumeroALetras\NumeroALetras;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Cotizacion;
use App\Models\Repuesto;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AutoboPedido;
use App\Mail\AutoboRespuestaConsulta;
use App\Mail\AutoboRespuestaFinal;
use App\Mail\AutoboPago;

class AdminController extends Controller
{

    public function pedido(Request $request)
    {
        $codigo = $request->codigo;
        $cotizacion = Cotizacion::where('codigo', $codigo)->first();
        $cotizacion->estado = 'En espera de confirmacion de pago';
        $cotizacion->save();

        $total = 0;
        foreach ($cotizacion->repuestos as $repuesto) {
            if ($repuesto->check == 1 && $repuesto->check2 == 1) {
                $total = $total + ($repuesto->precio * $repuesto->cantidad);
            }
        }
        
        $client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
        $client->messages->create(
            $cotizacion->telefono,
            array(
                'from' => env('TWILIO_FROM_NUMBER'),
                'body' => 'AUTOBO: Estimado cliente, para continuar con su pedido ' . $cotizacion->codigo . ' deposite Bs. ' . $total . ' al numero de cuenta 1000002154 del BANCO UNION. Gracias.'
            )
        );




        $destinatario = env('CORREO_BOLIVIA');
        Mail::to($destinatario)
            ->send(new AutoboPedido($cotizacion));

        return response()->json([
            'success' => true,
        ]);
    }
    public function pedido2(Request $request)
    {
        $codigo = $request->codigo;
        $cotizacion = Cotizacion::with(['repuestos', 'repuestos.imagenes'])->where('codigo', $codigo)->first();


        if ($cotizacion) {

            $total = 0;
            foreach ($cotizacion->repuestos as $repuesto) {
                if ($repuesto->check == 1 && $repuesto->check2 == 1) {
                    $total = $total + ($repuesto->precio * $repuesto->cantidad);
                }
            }

            if ($cotizacion->estado == 'En espera de aceptacion') {
                return response()->json([
                    'success' => 'existe',
                    'cotizacion' => $cotizacion
                ]);
            }
            if ($cotizacion->estado == 'En espera de aceptacion+') {
                return response()->json([
                    'success' => 'existe+',
                    'total' => $total,
                    'cotizacion' => $cotizacion
                ]);
            }
            return response()->json([
                'success' => 'aceptado',
            ]);
        } else {
            return response()->json([
                'success' => 'no existe',
            ]);
        }
    }

    public function pedido3(Request $request)
    {
        if ($request->repuestos) {

            foreach ($request->repuestos as $repuesto) {
                $repu = Repuesto::find($repuesto);
                $repu->check2 = '1';
                $repu->save();
                $cotizacion_id = $repu->cotizacion_id;
            }


            $cotizacion = Cotizacion::find($cotizacion_id);

            $cotizacion->estado = 'En espera de aceptacion+';
            $cotizacion->save();

            $total = 0;
            foreach ($cotizacion->repuestos as $repuesto) {
                if ($repuesto->check && $repuesto->check2) {
                    $total = $total + ($repuesto->precio * $repuesto->cantidad);
                }
            }

            $formatter = new NumeroALetras();
            $texto = $formatter->toInvoice($total, 2, 'Bolivianos');
            $pdf = PDF::loadView('pdf.pdf', compact('cotizacion', 'texto'));
            $pdf->setpaper('A4');
            $pdf->save(public_path('/pdf/' . $cotizacion->codigo . '.pdf'));


            return response()->json([
                'success' => true,
            ]);
        }
    }




    public function respuesta(Request $request)
    {
        $id = $request->id;
        $ejecutivo = $request->ejecutivo;
        $marca = $request->marca;
        $modelo = $request->modelo;
        $año = $request->año;
        $obs = $request->obs;
        $cotizacion = Cotizacion::find($id);

        $cotizacion->ejecutivo = $ejecutivo;
        $cotizacion->marca = $marca;
        $cotizacion->modelo = $modelo;
        $cotizacion->año = $año;
        $cotizacion->obs = $obs;

        $count = 0;
        $total = 0;
        foreach ($request->repuestos as $repuesto) {
            if (isset($repuesto['check']) && $repuesto['check']) {
                $repu = Repuesto::find($repuesto['id']);
                $repu->check = $repuesto['check'];
                $repu->precio = $repuesto['precio'];
                $repu->marca = $repuesto['marca'];
                $repu->procedencia = $repuesto['procedencia'];
                $repu->save();
                $count++;
                $total = $total + ($repu->precio * $repu->cantidad);
            }
        }
        $cotizacion->estado = 'En espera de aceptacion';
        $cotizacion->save();

        if ($count > 0) {
            /*
            $formatter = new NumeroALetras();
            $texto = $formatter->toInvoice($total, 2, 'Bolivianos');
            $pdf = PDF::loadView('pdf.pdf', compact('cotizacion', 'texto'));
            $pdf->setpaper('A4')
            $pdf->save(public_path('/pdf/' . $cotizacion->codigo . '.pdf'));
            */
            
            $client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
            $client->messages->create(
                $cotizacion->telefono,
                array(
                    'from' => env('TWILIO_FROM_NUMBER'),
                    'body' => 'AUTOBO: Estimado cliente, su consulta ' . $cotizacion->codigo . ' fue respondida. Para ver la informacion ingresa a http://144.22.54.25/invitado/pedido/' . $cotizacion->codigo
                )
            );
            
        } else {
            $cotizacion->estado = 'Cancelado';
            $cotizacion->save();
            
            $client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
            $client->messages->create(
                $cotizacion->telefono,
                array(
                    'from' => env('TWILIO_FROM_NUMBER'),
                    'body' => 'AUTOBO: Estimado cliente, actualmente no tenemos en stock ninguno de sus repuestos para su consulta ' . $cotizacion->codigo . ', por favor realice nuevamente la consulta en 30 dias. Gracias'
                )
            );
            
        }

        $destinatario = $cotizacion->email;
        Mail::to($destinatario)
            ->send(new AutoboRespuestaConsulta($cotizacion));


        return response()->json([
            'success' => true,
        ]);
    }



    public function respuesta2(Request $request)
    {
        $codigo = $request->codigo;
        $cotizacion = Cotizacion::where('codigo', $codigo)->first();

        $cotizacion->estado = 'En proceso de pedido';
        $cotizacion->save();
        
        $destinatario = $cotizacion->email;
        Mail::to($destinatario)
            ->send(new AutoboPago($cotizacion));
        
        
        
        
        $client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
        $client->messages->create(
            $cotizacion->telefono,
            array(
                'from' => env('TWILIO_FROM_NUMBER'),
                'body' => 'AUTOBO: Estimado cliente, recibimos el pago por su pedido ' . $cotizacion->codigo . '. Gracias por su confianza.'
            )
        );


        return response()->json([
            'success' => true,
        ]);
    }


    public function respuesta3(Request $request)
    {
        $codigo = $request->codigo;
        $cotizacion = Cotizacion::where('codigo', $codigo)->first();

        $cotizacion->estado = 'Piezas en locacion';
        $cotizacion->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function respuesta4(Request $request)
    {
        $codigo = $request->codigo;
        $cotizacion = Cotizacion::where('codigo', $codigo)->first();

        $cotizacion->estado = 'Despachado';
        $cotizacion->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function respuesta5(Request $request)
    {
        $codigo = $request->codigo;
        $cotizacion = Cotizacion::where('codigo', $codigo)->first();

        $cotizacion->estado = 'Recepcionado en Bolivia';
        $cotizacion->save();


        $destinatario = $cotizacion->email;
        Mail::to($destinatario)
            ->send(new AutoboRespuestaFinal($cotizacion));


        
        $client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
        $client->messages->create(
            $cotizacion->telefono,
            array(
                'from' => env('TWILIO_FROM_NUMBER'),
                'body' => 'AUTOBO: Estimado cliente, su pedido ' . $cotizacion->codigo . ' esta aquí, por favor dirijase a la agencia de Autobo ubicado en la Avenida Hernan Siles Suazo Nro 20 La Paz'
            )
        );


        return response()->json([
            'success' => true,
        ]);
    }

    public function respuesta6(Request $request)
    {
        $codigo = $request->codigo;
        $cotizacion = Cotizacion::where('codigo', $codigo)->first();

        $cotizacion->estado = 'Finalizado';
        $cotizacion->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function respuesta7(Request $request)
    {
        $codigo = $request->codigo;
        $cotizacion = Cotizacion::where('codigo', $codigo)->first();

        $cotizacion->estado = 'Cancelado';
        $cotizacion->save();

        return response()->json([
            'success' => true,
        ]);
    }
}

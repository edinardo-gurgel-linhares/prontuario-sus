<?php

namespace App\Http\Controllers;

use App\Model\Usuario;
use App\Model\Permissao;
use \App\Model\Secretario;
use App\Http\Requests\UsuarioRequest;
use Illuminate\Http\Request;
use App\Http\Requests\SenhaRequest;

class UsuarioController extends Controller
{
    public function secretarios($id) {
        $usuario = Usuario::find($id);
        $inclusos = Permissao::where('medico_id', $id)->get();
        $secretarios = Secretario::all();
        $inc = [];

        foreach($inclusos as $i)
            $inc[] = $i->sec_id;


        return view('secretarios', compact('usuario', 'secretarios', 'inc'));
    }

    public function trocarSecretarios(Request $request, $id)
    {
        $usuarios = Permissao::where('medico_id', $id)->get();
        foreach ($usuarios as $user) {
            $user->delete();
        }

        if($request->has('sec')) {
            foreach ($request->sec as $user) {
                Permissao::create([
                    'medico_id' => $id,
                    'sec_id' => $user
                ]);
            }
        }


        return redirect('usuarios/secretarios/'.$id)->withMsg('Secretários salvos!');
    }

    public function bloquear($id)
    {
    	$usuario = Usuario::find($id);
    	$usuario->valido = 0;
    	$usuario->save();
    	return redirect($_SERVER['HTTP_REFERER'])->withMsg($usuario->nome . ' foi bloqueada(o)!');
    }

    public function desbloquear($id)
    {
    	$usuario = Usuario::find($id);
    	$usuario->valido = 1;
    	$usuario->save();

    	return redirect($_SERVER['HTTP_REFERER'])->withMsg($usuario->nome . ' foi desbloqueada(o)!');
    }

    public function apagar($id)
    {
        $usuario = Usuario::find($id);
        $usuario->delete();

        return redirect($_SERVER['HTTP_REFERER'])->withMsg($usuario->nome . ' foi apagada(o)!');
    }

    public function redefinir($id)
    {
        $usuario = Usuario::find($id);
        $usuario->senha = bcrypt($usuario->cpf);
        $usuario->save();

        return redirect($_SERVER['HTTP_REFERER'])->withMsg($usuario->nome . ' teve a senha redefinida!');
    }

    public function perfil()
    {
        return view('perfil');
    }

    public function salvarPerfil(UsuarioRequest $requisicao)
    {
        if($requisicao->senha != null)
            $requisicao->merge(['senha' => bcrypt($requisicao->senha)]);

        auth()->user()->fill($requisicao->all());
        auth()->user()->save();

        if(auth()->user()->medico) {
            auth()->user()->medico->fill($requisicao->all());
            auth()->user()->medico->save();
        }
        else  if(auth()->user()->secretario) {
            auth()->user()->secretario->fill($requisicao->all());
            auth()->user()->secretario->save();
        }


        return redirect('perfil')->withMsg('Perfil atualizado!');
    }

    public function alterarSenha(SenhaRequest $requisicao)
    {
        $requisicao->merge(['senha' => bcrypt($requisicao->senha)]);

        auth()->user()->fill($requisicao->all());
        auth()->user()->save();

        return redirect('perfil')->withMsg('Senha atualizada!');
    }

}

<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PerfilEstudante;
use App\Models\PersonalidadeAluno;
use App\Models\Comunicacao;
use App\Models\Preferencia;
use App\Models\PerfilFamilia;
use Illuminate\Support\Facades\DB;

class AtualizacaoPerfilController extends Controller
{
    public function AtualizaPerfil(Request $request, $id)
    {
        // Validação básica dos campos essenciais
        $request->validate([
            'diag_laudo' => 'required|numeric',
            'nivel_suporte' => 'required|numeric',
            'precisa_comunicacao' => 'required|numeric'
        ]);

        DB::beginTransaction();
        try {
            // Atualização usando relacionamento por fk_id_aluno
            $this->atualizarPerfilEstudante($request, $id);
            $this->atualizarPersonalidade($request, $id);
            $this->atualizarComunicacao($request, $id);
            $this->atualizarPreferencia($request, $id);
            $this->atualizarPerfilFamilia($request, $id);

            DB::commit();
            return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    private function atualizarPerfilEstudante($request, $id)
    {
        $perfil = PerfilEstudante::where('fk_id_aluno', $id)->firstOrFail();
        
        $perfil->update([
            'diag_laudo' => $request->diag_laudo,
            'cid' => $request->cid,
            'nome_medico' => $request->nome_medico,
            'data_laudo' => $request->data_laudo,
            'nivel_suporte' => $request->nivel_suporte,
            'uso_medicamento' => $request->uso_medicamento,
            'quais_medicamento' => $request->quais_medicamento,
            'nec_pro_apoio' => $request->nec_pro_apoio,
            'prof_apoio' => $request->prof_apoio,
            'loc_01' => $request->has('locomocao') ? 1 : 0,
            'hig_02' => $request->has('higiene') ? 1 : 0,
            'ali_03' => $request->has('alimentacao') ? 1 : 0,
            'com_04' => $request->has('comunicacao') ? 1 : 0,
            'out_05' => $request->has('outros') ? 1 : 0,
            'out_momentos' => $request->out_momentos,
            'at_especializado' => $request->at_especializado,
            'nome_prof_AEE' => $request->nome_prof_AEE
        ]);
    }

    private function atualizarPersonalidade($request, $id)
    {
        
        $model = PersonalidadeAluno::where('fk_id_aluno', $id)->firstOrFail();
        
        $model->update([
            'carac_principal' => $request->caracteristicas,
            'inter_princ_carac' => $request->areas_interesse,
            'livre_gosta_fazer' => $request->atividades_livre,
            'feliz_est' => $request->feliz,
            'trist_est' => $request->triste,
            'obj_apego' => $request->objeto_apego
        ]);
    }

    private function atualizarComunicacao($request, $id)
    {
        $model = Comunicacao::where('fk_id_aluno', $id)->firstOrFail();
        
        $model->update([
            'precisa_comunicacao' => $request->precisa_comunicacao,
            'entende_instrucao' => $request->entende_instrucao,
            'recomenda_instrucao' => $request->recomenda_instrucao
        ]);
    }

    private function atualizarPreferencia($request, $id)
    {
        $model = Preferencia::where('fk_id_aluno', $id)->firstOrFail();
        
        $model->update([
            'auditivo_04' => $request->has('s_auditiva') ? 1 : 0,
            'visual_04' => $request->has('s_visual') ? 1 : 0,
            'tatil_04' => $request->has('s_tatil') ? 1 : 0,
            'outros_04' => $request->has('s_outros') ? 1 : 0,
            'maneja_04' => $request->manejo_sensibilidade,
            'asa_04' => $request->seletividade_alimentar,
            'alimentos_pref_04' => $request->alimentos_preferidos,
            'alimento_evita_04' => $request->alimentos_evita,
            'contato_pc_04' => $request->afinidade_escola,
            'reage_contato' => $request->reacao_contato,
            'interacao_escola_04' => $request->interacao_escola,
            'interesse_atividade_04' => $request->interesse_atividade,
            'aprende_visual_04' => $request->has('r_visual') ? 1 : 0,
            'recurso_auditivo_04' => $request->has('r_auditivo') ? 1 : 0,
            'material_concreto_04' => $request->has('m_concreto') ? 1 : 0,
            'outro_identificar_04' => $request->has('o_outro') ? 1 : 0,
            'descricao_outro_identificar_04' => $request->outro_identificar,
            'realiza_tarefa_04' => $request->atividades_grupo,
            'mostram_eficazes_04' => $request->estrategias_eficazes,
            'prefere_ts_04' => $request->interesse_tarefa
        ]);
    }

    private function atualizarPerfilFamilia($request, $id)
    {
        PerfilFamilia::updateOrCreate(
            ['fk_id_aluno' => $id],
            [
                'expectativa_05' => $request->expectativas_familia,
                'estrategia_05' => $request->estrategias_familia,
                'crise_esta_05' => $request->crise_estresse
            ]
        );
    }
}

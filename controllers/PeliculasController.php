<?php

namespace app\controllers;

use app\models\PeliculasForm;
use Yii;
use yii\data\Sort;
use yii\web\NotFoundHttpException;

/**
 * Definición del controlador peliculas.
 */
class PeliculasController extends \yii\web\Controller
{
    public function actionPrueba()
    {
        Yii::$app->session->setFlash('error', 'Esto es un error.');
        return $this->redirect(['peliculas/index']);
    }

    public function actionIndex()
    {
        $sort = new Sort([
            'attributes' => [
                'titulo',
                'anyo',
                'duracion',
                'genero',
            ],
        ]);

        if (empty($sort->orders)) {
            $orderBy = '1';
        } else {
            $res = [];
            foreach ($sort->orders as $columna => $sentido) {
                $res[] = $sentido == SORT_ASC ? "$columna ASC" : "$columna DESC";
            }
            $orderBy = implode(',', $res);
        }

        $filas = \Yii::$app->db
            ->createCommand("SELECT p.*, g.genero
                               FROM peliculas p
                               JOIN generos g
                                 ON p.genero_id = g.id
                           ORDER BY $orderBy")->queryAll();
        return $this->render('index', [
            'filas' => $filas,
            'sort' => $sort,
        ]);
    }

    public function actionCreate()
    {
        $peliculasForm = new PeliculasForm();

        if ($peliculasForm->load(Yii::$app->request->post()) && $peliculasForm->validate()) {
            Yii::$app->db->createCommand()
                ->insert('peliculas', $peliculasForm->attributes)
                ->execute();
            return $this->redirect(['peliculas/index']);
        }
        return $this->render('create', [
            'peliculasForm' => $peliculasForm,
        ]);
    }

    public function actionUpdate($id)
    {
        $peliculasForm = new PeliculasForm(['attributes' => $this->buscarPelicula($id)]);

        if ($peliculasForm->load(Yii::$app->request->post()) && $peliculasForm->validate()) {
            Yii::$app->db->createCommand()
                ->update('peliculas', $peliculasForm->attributes, ['id' => $id])
                ->execute();
            return $this->redirect(['peliculas/index']);
        }

        return $this->render('update', [
            'peliculasForm' => $peliculasForm,
            'listaGeneros' => $this->listaGeneros(),
            'participantes' => $this->buscarParticipantes($id),
        ]);
    }

    public function actionDelete($id)
    {
        Yii::$app->db->createCommand()->delete('peliculas', ['id' => $id])->execute();
        return $this->redirect(['peliculas/index']);
    }

    public function actionVer($id)
    {
        return $this->render('ver', [
            'pelicula' => $this->buscarPelicula($id),
            'participantes' => $this->buscarParticipantes($id),
        ]);
    }

    private function listaGeneros()
    {
        $generos = Yii::$app->db->createCommand('SELECT * FROM generos')->queryAll();
        $listaGeneros = [];
        foreach ($generos as $genero) {
            $listaGeneros[$genero['id']] = $genero['genero'];
        }
        return $listaGeneros;
    }

    private function buscarPelicula($id)
    {
        $fila = Yii::$app->db
            ->createCommand('SELECT p.*, genero
                               FROM peliculas p
                               JOIN generos g
                                 ON genero_id = g.id
                              WHERE p.id = :id', [':id' => $id])->queryOne();
        if ($fila === false) {
            throw new NotFoundHttpException('Esa película no existe.');
        }
        return $fila;
    }

    private function buscarParticipantes($id)
    {
        return Yii::$app->db
            ->createCommand('SELECT pa.*, nombre, rol
                               FROM participantes pa
                               JOIN roles r
                                 ON rol_id = r.id
                               JOIN personas p
                                 ON persona_id = p.id
                              WHERE pelicula_id = :id', [':id' => $id])
            ->queryAll();
    }
}

<?php

class TicketStatusController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $user_data;

    protected function beforeAction($event) {
        if (!isset(Yii::app()->session['user_data'])) {
            $this->redirect(Yii::app()->request->baseUrl . '/auth');
        }

        $this->user_data = Yii::app()->session['user_data'];

        return true;
    }

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow authenticated users to access all actions
                'users' => array(Yii::app()->user->name),
            ),
            array('deny'),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
   public function actionCreate() {
        $model = new TicketStatus;

        // Uncomment the following line if AJAX validation is needed
        $this->performAjaxValidation($model);

        if (isset($_POST['TicketStatus'])) {
            $model->attributes = $_POST['TicketStatus'];            

            if ($model->save()) {
                Yii::app()->user->setFlash('type', 'success');
                Yii::app()->user->setFlash('message', 'Ticker Status added successfully.');
            } else {
                Yii::app()->user->setFlash('type', 'danger');
                Yii::app()->user->setFlash('message', 'Operation failded due to lack of connectivity. Try again later!!!');
            }
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
     public function actionUpdate($id) {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        $this->performAjaxValidation($model);

        if (isset($_POST['TicketStatus'])) {
            $model->attributes = $_POST['TicketStatus'];
            $model->updated_date = Date("Y-m-d H:i:s");

            if ($model->update()) {
                Yii::app()->user->setFlash('type', 'success');
                Yii::app()->user->setFlash('message', 'Ticket Status updated successfully.');
            } else {
                Yii::app()->user->setFlash('type', 'danger');
                Yii::app()->user->setFlash('message', 'Operation failded due to lack of connectivity. Try again later!!!');
            }

            $this->redirect(array('index'));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
     public function actionDelete($id) {
          $model = $this->loadModel($id);
          $status = Ticket::checkStatus($id);
        if ($status) {
            if (!isset($_GET['ajax'])) {
                Yii::app()->user->setFlash('type', 'warning');
                Yii::app()->user->setFlash('message', 'Could Not Delete ticket status Because status is atthched with Ticket.');
            } else {
                echo '<div class="alert alert-warning alert-dismissable" id="successmsg">Could Not Delete ticket status Because status is atthched with ticket.</div>';
            }
        } else {
            $model->delete();
            if (!isset($_GET['ajax'])) {
                Yii::app()->user->setFlash('type', 'success');
                Yii::app()->user->setFlash('message', 'Ticket Status removed successfully.');
            } else {
                echo '<div class="alert alert-success alert-dismissable" id="successmsg">Ticket Status removed successfully.</div>';
            }
        }
    }

    /**
     * Manages all models.
     */
    public function actionIndex() {
        $model = new TicketStatus('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['TicketStatus']))
            $model->attributes = $_GET['TicketStatus'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return TicketStatus the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = TicketStatus::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param TicketStatus $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'ticket-status-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}

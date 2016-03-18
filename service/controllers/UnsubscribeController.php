<?php

class UnsubscribeController extends Controller
{

    public function accessRules ()
    {
        return array_merge(
            array(
                array(
                    'allow',
                    'users' => array('?')
                )
            ),
            parent::accessRules() // Include parent access rules
        );
    }

    public function actionUnsub ()
    {
        $email = !isset($_GET['md_email'])
                ? false
                : urldecode($_GET['md_email']);

        $user = new User();
        $criteria = new CDbCriteria();
        $criteria->condition = "email = :email";
        $criteria->params = array(
          ':email' => $email,
        );
        $user = $user->find($criteria);
        $user->is_unsubscribed = 1;
        $user->save();
    }

}
?>

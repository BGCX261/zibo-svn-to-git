<?php

namespace zibo\error\report\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\Zibo;

use zibo\error\report\form\ReportForm;
use zibo\error\report\view\ReportView;
use zibo\error\report\Module;

use zibo\library\mail\Message;
use zibo\library\Session;

/**
 * Controller to ask the user's comment on the occured error and send it through email to the developers
 */
class ReportController extends AbstractController {

    /**
     * Translation key for the message when the report has been sent
     * @var string
     */
    const TRANSLATION_MAIL_SENT = 'error.report.message.mail.sent';

    /**
     * Action to ask for extra information and to send the error report
     * @return null
     */
    public function indexAction() {
        $zibo = Zibo::getInstance();
        $session = Session::getInstance();

        $recipient = $zibo->getConfigValue(Module::CONFIG_MAIL_RECIPIENT);
        $subject = $zibo->getConfigValue(Module::CONFIG_MAIL_SUBJECT);

        $report = $session->get(Module::SESSION_REPORT);
        if (!$report || !$recipient) {
            $this->response->setRedirect($this->request->getBaseUrl());
            return;
        }

        $form = new ReportForm($this->request->getBasePath());
        if ($form->isSubmitted()) {
            $comment = $form->getComment();
            if ($comment) {
                $report .= "\n\nComment:\n" . $comment;
            }

            if (!$subject) {
                list($subject, $null) = explode("\n", $report, 2);
            }

            $mail = new Message();
            $mail->setTo($recipient);
            $mail->setSubject($subject);
            $mail->setMessage($report);
            $mail->send();

            $session->set(Module::SESSION_REPORT);

            $this->addInformation(self::TRANSLATION_MAIL_SENT);
            $this->response->setRedirect($this->request->getBaseUrl());
            return;
        }

        $view = new ReportView($form, $report);
        $this->response->setView($view);
    }

}
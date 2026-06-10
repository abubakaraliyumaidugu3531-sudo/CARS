<?php
// ApprovalController: Handles advisor approvals
require_once __DIR__ . '/../models/ApprovalModel.php';

class ApprovalController {
    private $approvalModel;
    public function __construct() {
        $this->approvalModel = new ApprovalModel();
    }
    public function submit($advisor_id, $student_id, $status) {
        return $this->approvalModel->submit($advisor_id, $student_id, $status);
    }
    public function getByAdvisor($advisor_id) {
        return $this->approvalModel->getByAdvisor($advisor_id);
    }
    public function getByStudent($student_id) {
        return $this->approvalModel->getByStudent($student_id);
    }
}

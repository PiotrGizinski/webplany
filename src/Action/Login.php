<?PHP

namespace Action {

    class Login extends \Engine\Action {   

        public function execute() : void {
            if($this->reg->getAuthorization()->getLogged()) {
                $this->reg->getAuthorization()->signOut();
            } else {
                $this->reg->getAuthorization()->signIn();
            }
            unset($_REQUEST['ticket']);
            header("Location: ".ROOT);
        }
    }
}

?>
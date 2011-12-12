Event
=====

    class Foo
    {
        public $event;

        public function send($foo, $bar)
        {
            $this->event->notify('foo.before_send', array('foo' => $foo, 'bar' => $bar));
            $ret = 'ret';
            $this->event->notify('foo.after_send', array('ret' => $ret));
            return $ret;
        }
    }

    class Bar
    {
        public function onFooBeforeSend($event)
        {
            $foo = $event->getParam('foo');
            $bar = $event->getParam('bar');
            echo 'before param: ' . $foo . ', ' . $bar;
            return $event;
        }

        public function onFooAfterSend($event)
        {
            $ret = $event->getParam('ret');
            echo 'after param: ' . $ret;
            return $event;
        }
    }

$event = new Event();
$event->connect('foo.before_send', 'Bar', 'onFooBeforeSend');
$event->connect('foo.after_send', 'Bar', 'onFooAfterSend');

$foo = new Foo();
$foo->event = $event;
$foo->send('foo value', 'bar value');

$this->bind('Foo')->event('foo.before_send', 'Bar', 'onFooAfterSend');


<?php

if (!class_exists(\Twig\Node\Node::class, false) && class_exists('Twig_Node', false)) {
    class_alias('Twig_Node', \Twig\Node\Node::class, false);
}

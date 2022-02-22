<div class="paging">
    <?php
    if ($this->Paginator->hasPrev()) {
        echo $this->Paginator->first(__('<< primera '), [], null, ['class' => 'prev disabled']);
        echo "|";
        echo $this->Paginator->prev(__(' anterior'), [], null, ['class' => 'prev disabled']);
    }
    echo "&nbsp;&nbsp;" . $this->Paginator->numbers(['separator' => '&nbsp;&nbsp;|&nbsp;&nbsp;']) . "&nbsp;&nbsp;";
    if ($this->Paginator->hasNext()) {
        echo $this->Paginator->next(__(' siguiente '), [], null, ['class' => 'next disabled']);
        echo "|";
        echo $this->Paginator->last(__(' última >>'), [], null, ['class' => 'next disabled']);
    }
    ?>
</div>
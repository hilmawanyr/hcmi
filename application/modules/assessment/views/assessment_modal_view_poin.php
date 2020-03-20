<div class="modal-header">
    <h4 class="modal-title" id="title-mod"><?= $dictionary->name_id ?> | <?= user_name($nik) ?></h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th style="white-space:nowrap;">Pernyataan</th>
                        <th style="white-space:nowrap;">Poin</th>
                        <!-- <th style="white-space:nowrap;">Bobot</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($poin) < 1) : ?>
                        <tr>
                            <td colspan="3"><i>No data available</i></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($poin as $poins) : ?>
                            <tr>
                                <td><?= $poins->description ?></td>
                                <td><?= $poins->poin ?></td>
                                <!-- <td><?= $poins->weight ?></td> -->
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
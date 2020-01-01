<div class="table-wrap">
    <div class="table-responsive">
        <?php if ($group == 1 || $group == 2) : ?>
            <button 
                data-toggle="modal" 
                data-target="#addModal<?= $skillTypeId ?>" 
                onclick="load_modal(1,<?= $skillTypeId ?>)" 
                class="btn btn-info">
                <i class="fa fa-plus"></i> Add Competency
            </button>
        <?php endif; ?>
        
        <a 
            href="<?= base_url('dictionary/'.$skillTypeId.'/print') ?>" 
            class="btn btn-primary">
            <i class="fa fa-print"></i> Export to Excel
        </a>
        <hr>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Indonesian Name</th>
                    <th>Description</th>
                    <th>Competency Type</th>
                    <th width="140">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach ($dictionaries as $dictionary) : ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= $dictionary->name_id ?></td>
                        <td><?= $dictionary->description ?></td>
                        <td><?= get_skill_type_name($dictionary->skill_group) ?></td>
                        <td>
                            <a 
                                href="<?= base_url('skill_unit/'.$dictionary->id.'/dictionary') ?>" 
                                class="btn btn-primary"><i class="fa fa-list"></i></a>
                            <?php if ($group == 1 || $group == 2) : ?>
                                <a 
                                    data-toggle="modal" 
                                    class="btn btn-warning"
                                    href="#addModal<?= $dictionary->skill_group ?>" 
                                    onclick="load_modal(2, <?= $dictionary->id ?>)"><i class="fa fa-pencil"></i></a>
                                <a 
                                    href="<?= base_url('dictionary/'.$dictionary->id.'/remove') ?>" 
                                    class="btn btn-danger"
                                    onclick="return confirm('Are you sure to remove this competency?')">
                                    <i class="fa fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php $no++; endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="addModal<?= $skillTypeId ?>" class="modal fade" role="dialog">
    <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"></h4>
        </div>
        <form action="<?= base_url('dictionary/store') ?>" id="formAction" method="post">
            <div class="modal-body">
                <input type="hidden" name="isUpdate" value="">
                <div class="form-group">
                    <label for="name_id">Competency Type</label>
                    <select name="skill_type" id="" class="form-control">
                        <?php foreach ($skillTypes as $type) { ?>
                            <option value="<?= $type->id ?>" <?= $type->id == $skillTypeId ? 'selected=""' : "" ?> ><?= $type->name ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name_id">Indonesian Name</label>
                    <input type="text" class="form-control" id="name_id" value="" name="name_id">
                </div>
                <div class="form-group">
                    <label for="name_en">English Name</label>
                    <input type="text" class="form-control" id="name_en" value="" name="name_en">
                </div>
                <div class="form-group">
                    <label for="name_id">Decription</label>
                    <textarea name="description" class="form-control" id="description" rows="5"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="btnSubmit" class="btn btn-primary"></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>

    </div>
</div>

<script>
    function load_modal(actionType, skillTypeId) {
        switch (actionType) {
            // for add competency dictionary data
            case 1:
                $('.modal-title').text('Add Competency Dictionary');
                $('input[name="isUpdate"]').val('');
                $('input[name="name_id"]').val('');
                $('input[name="name_en"]').val('');
                $('textarea#description').val('');
                $('#btnSubmit').text('Save');
                break;
            // for edit competency dictionary data
            default:
                $('.modal-title').text('Edit Competency Dictionary');
                $('#btnSubmit').text('Update');
                $.get('<?= base_url() ?>dictionary/' + skillTypeId + '/edit', function(response){
                    var res = JSON.parse(response);
                    console.log(res)
                    $('input[name="isUpdate"]').val(res.id);
                    $('input[name="name_id"]').val(res.name_id);
                    $('input[name="name_en"]').val(res.name_en);
                    $('textarea#description').val(res.description);
                })
                break;
        }
    }
</script>
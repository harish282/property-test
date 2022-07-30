<table class="table table-striped table-bordered" id="proptable">
    <thead>
        <tr>
            <th>Town</th>
            <th># Bedrooms</th>
            <th>Price</th>
            <th>Property Type</th>
            <th>For Sale/Rent</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($properties as $property):?>
        <tr>
            <td><?php echo $property->town?></td>
            <td><?php echo $property->num_bedrooms?></td>
            <td><?php echo number_format($property->price, 2)?></td>
            <td><?php echo $property_types[$property->property_type_id][0]->title ?></td>
            <td><?php echo $property->type ?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>

<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.12.1/datatables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#proptable').DataTable();
    });
</script>
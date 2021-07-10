<?php
function displayResult(array $result) {
?>
<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th scope="col" colspan="4"><?php echo $result[0][0]; ?></th>
        <th scope="col" colspan="4"><?php echo $result[0][4]; ?></th>
        <th scope="col" colspan="3"><?php echo $result[0][8]; ?></th>
        <?php
          $i = 0;
          foreach(array_slice($result[0], 11) as $question) {
              if ($i % 2 == 0) { ?>
                  <th scope="col" colspan="2"><?php echo $question; ?></th>
        <?php } $i+=1; } ?>
      </tr>
      <tr>
        <?php foreach($result[1] as $header) { ?>
          <th scope="col"><?php echo $header; ?></th>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach(array_slice($result, 2) as $student) { ?>
      <tr>
        <?php foreach($student as $column) { ?>
          <td><?php echo $column;?></td>
        <?php } ?>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
<?php }?>





                          <?= $form->field($model1, 'text')->widget(Select2::classname(), [        
                          'language' => 'es',
                          'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                          'pluginOptions' => [
                              'multiple'=>true,
                              'allowClear' => false,
                              'minimumInputLength' => 4,
                              'ajax' => [
                                  'url' => Url::to(['hobbies']),
                                  'dataType' => 'json',
                                  'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                  'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                              ],
                              'initSelection' => new JsExpression('function (element, callback) {
                                                  var id=$(element).val();
                                                  if (id !== "") {
                                                      $.ajax("'.Url::to(['hobbies']).'?id=" + id, {
                                                          dataType: "json",
                                                          type: "post"
                                                      }).done(function(data) { callback(data.results);});
                                                  }
                                              }')
                              ]
                          ]
                      );?>

 
                         



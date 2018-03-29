<div ng-controller="AnnotatorController">
<p>Total number of phones: {{phones.length}}</p>
  <ul>
    <li ng-repeat="phone in phones">
      <span>{{phone.name}}</span>
      <p>{{phone.snippet}}</p>
    </li>
  </ul>

</div>

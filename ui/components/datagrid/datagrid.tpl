
  <block:pageNavigator>

      <div class="ctitle_outer clearAfter">
        <block:quickGuide>
            <div class="dataGridNav">
                <label for="{{navigatorId}}_page" class="floatLeft">Page: </label>
                <input id="{{navigatorId}}_page" name="{{navigatorId}}_page" class="pageGo floatLeft" type="text" value="{{currPage}}" />
                <input id="{{navigatorId}}_btnPage" name="{{navigatorId}}_btnPage" class="pageGoBt floatLeft" type="image" align="top" value="" src="~~/images/components/datagrid/bt.png" />
                <span>of {{pagesCount}}</span>
            </div>
        </block:quickGuide>

        <div class="dataGridNav dataGridNavP">
            <ul class="ctitl_dataGrid cdNavL">
                <block:firstPage><li><a href="{{url}}" class="prevP">&nbsp;</a></li></block:firstPage>
                <block:prevPage><li><a href="{{url}}" class="prev">&nbsp;</a></li></block:prevPage>
            </ul>

            <ul class="ctitl_dataGrid cdNavPage">
                <block:currPage>
                    <block:active><li><a href="{{url}}" title="">{{pageNum}}</a></li></block:active>
                    <block:passive><li class="selected"><a href="javascript:void(0);" title="">{{pageNum}}</a></li></block:passive>
                </block:currPage>
            </ul>

            <ul class="ctitl_dataGrid cdNavR">
                <block:nextPage><li><a href="{{url}}" class="next">&nbsp;</a></li></block:nextPage>
                <block:lastPage><li><a href="{{url}}" class="nextP">&nbsp;</a></li></block:lastPage>
            </ul>
        </div>

        <block:customize>
            <div class="dataGridNav">
                <label for={{navigatorId}}_pageSize class="floatLeft">Display: </label>
                <input id="{{navigatorId}}_pageSize" name="{{navigatorId}}_pageSize" class="pageDisplay floatLeft" maxlength="5" type="text" value="{{pageSize}}" />
                <input id="{{navigatorId}}_btnPageSize" name="{{navigatorId}}_btnPageSize" class="pageGoBt floatLeft" type="image" src="~~/images/components/datagrid/bt.png" align="top" value="" />
                <span>items on page</span>
            </div>
        </block:customize>

      </div>

  </block:pageNavigator>

{{pageNavigatorTop}}

<table{{attributes}}>

  <block:gridHeader>
    <block:gridRow>
      <tr{{attributes}}>
          <block:gridCell>
              <block:normal><th{{attributes}}>{{content}}</th></block:normal>
              <block:sort><th{{attributes}}><a href="{{sortUrl}}" class="{{sortOrder}}">{{content}}<b>&nbsp;</b></a></th></block:sort>
          </block:gridCell>
      </tr>
    </block:gridRow>
  </block:gridHeader>

  <block:gridBody>
    <block:gridRow>
      <tr{{attributes}}><block:gridCell><td{{attributes}}>{{content}}</td></block:gridCell></tr>
    </block:gridRow>
  </block:gridBody>

</table>

{{pageNavigatorBottom}}

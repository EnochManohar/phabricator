<?php

/*
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

final class DifferentialInlineCommentView extends AphrontView {

  private $inlineComment;
  private $onRight;
  private $buildScaffolding;
  private $handles;
  private $markupEngine;

  public function setInlineComment(DifferentialInlineComment $comment) {
    $this->inlineComment = $comment;
    return $this;
  }

  public function setOnRight($on_right) {
    $this->onRight = $on_right;
    return $this;
  }

  public function setBuildScaffolding($scaffold) {
    $this->buildScaffolding = $scaffold;
    return $this;
  }

  public function setHandles(array $handles) {
    $this->handles = $handles;
    return $this;
  }

  public function setMarkupEngine(PhutilMarkupEngine $engine) {
    $this->markupEngine = $engine;
    return $this;
  }

  public function render() {

    $inline = $this->inlineComment;

    $start = $inline->getLineNumber();
    $length = $inline->getLineLength();
    if ($length) {
      $end = $start + $length;
      $line = 'Lines '.number_format($start).'-'.number_format($end);
    } else {
      $line = 'Line '.number_format($start);
    }

    $metadata = array(
      'number' => $inline->getLineNumber(),
      'length' => $inline->getLineLength(),
      'on_right' => $this->onRight, // TODO
    );

    $sigil = 'differential-inline-comment';

    $links = 'xxx';
    $content = $inline->getContent();
    $handles = $this->handles;

    if ($links) {
      $links =
        '<span class="differential-inline-comment-links">'.
          $links.
        '</span>';
    }

    $content = $this->markupEngine->markupText($content);

    $markup = javelin_render_tag(
      'div',
      array(
        'class' => 'differential-inline-comment',
        'sigil' => $sigil,
        'meta'  => $metadata,
      ),
      '<div class="differential-inline-comment-head">'.
        $links.
        '<span class="differential-inline-comment-line">'.$line.'</span>'.
        $handles[$inline->getAuthorPHID()]->renderLink().
      '</div>'.
      $content);

    return $this->scaffoldMarkup($markup);
  }

  private function scaffoldMarkup($markup) {
    if (!$this->buildScaffolding) {
      return $markup;
    }

    if ($this->onRight) {
      return
        '<table>'.
          '<tr>'.
            '<th></th>'.
            '<td></td>'.
            '<th></th>'.
            '<td>'.$markup.'</td>'.
          '</tr>'.
        '</table>';
    } else {
      return
        '<table>'.
          '<tr>'.
            '<th></th>'.
            '<td>'.$markup.'</td>'.
            '<th></th>'.
            '<td></td>'.
          '</tr>'.
        '</table>';
    }
  }

}
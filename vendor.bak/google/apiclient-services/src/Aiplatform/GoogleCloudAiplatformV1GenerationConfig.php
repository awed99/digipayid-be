<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1GenerationConfig extends \Google\Collection
{
  protected $collection_key = 'stopSequences';
  /**
   * @var int
   */
  public $candidateCount;
  /**
   * @var float
   */
  public $frequencyPenalty;
  /**
   * @var int
   */
  public $maxOutputTokens;
  /**
   * @var float
   */
  public $presencePenalty;
  /**
   * @var string
   */
  public $responseMimeType;
  protected $responseSchemaType = GoogleCloudAiplatformV1Schema::class;
  protected $responseSchemaDataType = '';
  protected $routingConfigType = GoogleCloudAiplatformV1GenerationConfigRoutingConfig::class;
  protected $routingConfigDataType = '';
  /**
   * @var int
   */
  public $seed;
  /**
   * @var string[]
   */
  public $stopSequences;
  /**
   * @var float
   */
  public $temperature;
  /**
   * @var float
   */
  public $topK;
  /**
   * @var float
   */
  public $topP;

  /**
   * @param int
   */
  public function setCandidateCount($candidateCount)
  {
    $this->candidateCount = $candidateCount;
  }
  /**
   * @return int
   */
  public function getCandidateCount()
  {
    return $this->candidateCount;
  }
  /**
   * @param float
   */
  public function setFrequencyPenalty($frequencyPenalty)
  {
    $this->frequencyPenalty = $frequencyPenalty;
  }
  /**
   * @return float
   */
  public function getFrequencyPenalty()
  {
    return $this->frequencyPenalty;
  }
  /**
   * @param int
   */
  public function setMaxOutputTokens($maxOutputTokens)
  {
    $this->maxOutputTokens = $maxOutputTokens;
  }
  /**
   * @return int
   */
  public function getMaxOutputTokens()
  {
    return $this->maxOutputTokens;
  }
  /**
   * @param float
   */
  public function setPresencePenalty($presencePenalty)
  {
    $this->presencePenalty = $presencePenalty;
  }
  /**
   * @return float
   */
  public function getPresencePenalty()
  {
    return $this->presencePenalty;
  }
  /**
   * @param string
   */
  public function setResponseMimeType($responseMimeType)
  {
    $this->responseMimeType = $responseMimeType;
  }
  /**
   * @return string
   */
  public function getResponseMimeType()
  {
    return $this->responseMimeType;
  }
  /**
   * @param GoogleCloudAiplatformV1Schema
   */
  public function setResponseSchema(GoogleCloudAiplatformV1Schema $responseSchema)
  {
    $this->responseSchema = $responseSchema;
  }
  /**
   * @return GoogleCloudAiplatformV1Schema
   */
  public function getResponseSchema()
  {
    return $this->responseSchema;
  }
  /**
   * @param GoogleCloudAiplatformV1GenerationConfigRoutingConfig
   */
  public function setRoutingConfig(GoogleCloudAiplatformV1GenerationConfigRoutingConfig $routingConfig)
  {
    $this->routingConfig = $routingConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1GenerationConfigRoutingConfig
   */
  public function getRoutingConfig()
  {
    return $this->routingConfig;
  }
  /**
   * @param int
   */
  public function setSeed($seed)
  {
    $this->seed = $seed;
  }
  /**
   * @return int
   */
  public function getSeed()
  {
    return $this->seed;
  }
  /**
   * @param string[]
   */
  public function setStopSequences($stopSequences)
  {
    $this->stopSequences = $stopSequences;
  }
  /**
   * @return string[]
   */
  public function getStopSequences()
  {
    return $this->stopSequences;
  }
  /**
   * @param float
   */
  public function setTemperature($temperature)
  {
    $this->temperature = $temperature;
  }
  /**
   * @return float
   */
  public function getTemperature()
  {
    return $this->temperature;
  }
  /**
   * @param float
   */
  public function setTopK($topK)
  {
    $this->topK = $topK;
  }
  /**
   * @return float
   */
  public function getTopK()
  {
    return $this->topK;
  }
  /**
   * @param float
   */
  public function setTopP($topP)
  {
    $this->topP = $topP;
  }
  /**
   * @return float
   */
  public function getTopP()
  {
    return $this->topP;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerationConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerationConfig');

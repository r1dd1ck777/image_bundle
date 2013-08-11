<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Rid\Bundle\ImageBundle\Model;

class RidImage extends RidFile
{
    const NO_IMAGE_PATH = 'images/no-image.png';

    public function offsetSet($offset, $value) {}
    public function offsetExists($offset) {}
    public function offsetUnset($offset) {}
    public function offsetGet($offset)
    {
        if ($offset === 0) {
            // fix issue with asset version
            return $this->__toString();
        }

        return $this->getThumbnailFullPath($offset);
    }

    public function __call($thumbnailName, $args)
    {
        return $this->getThumbnailFullPath($thumbnailName);
    }

    public function __get($thumbnailName)
    {
        return $this->getThumbnailFullPath($thumbnailName);
    }

    // filename "name_no-image.jpg"
    public function getThumbnailFileName($name, $context = self::CONTEXT_ORIGIN)
    {
        if ($context == self::CONTEXT_ORIGIN){
            return $name.'_'.$this->getValue();
        }
        if ($context == self::CONTEXT_OLD){
            return $name.'_'.$this->getOldValue();
        }
    }

    // web path with filename "uploads/images/name_no-image.jpg"
    public function getThumbnailFullPath($name, $context = self::CONTEXT_ORIGIN)
    {
        if (!$this->isInit()){
            return self::NO_IMAGE_PATH;
        }

        if (!$this->hasValue()){
            return $this->getThumbnailDefaultFullPath($name);
        }

        if ($context == self::CONTEXT_ORIGIN){
            return $this->getOriginPath().$this->getThumbnailFileName($name);
        }
        if ($context == self::CONTEXT_OLD){
            return $this->getOriginPath().$this->getThumbnailFileName($name, $context);
        }
    }

    /** @return array */
    public function getThumbnailData($name)
    {
        return $this->config->getThumbnail($this->getPreset(), $name);
    }

    // system filename "/var/www/server/web/uploads/images/name_no-image.jpg"
    public function getThumbnailFullFileName($name, $context = self::CONTEXT_ORIGIN)
    {
        if ($context == self::CONTEXT_ORIGIN){
            return $this->getOriginDir(). $this->getThumbnailFileName($name);
        }
        if ($context == self::CONTEXT_OLD){
            return $this->getOriginDir().$this->getThumbnailFileName($name, $context);
        }
    }

    public function getThumbnailNames()
    {
        return $this->config->getThumbnailNames($this->getPreset());
    }

    public function getOriginFullPath($context = self::CONTEXT_ORIGIN)
    {
        if (!$this->isInit()){
            return self::NO_IMAGE_PATH;
        }

        if (!$this->hasValue()){
            return $this->getDefaultFullPath();
        }

        return parent::getOriginFullPath($context);
    }

    public function getDefaultFullPath()
    {
        $path = $this->config->getDefaultFullPath($this->getPreset());
        return !is_null($path) ? $path : self::NO_IMAGE_PATH;
    }

    public function getThumbnailDefaultFullPath($name)
    {
        $path = $this->config->getThumbnailDefaultFullPath($this->getPreset(), $name);
        return !is_null($path) ? $path : self::NO_IMAGE_PATH;
    }
}
